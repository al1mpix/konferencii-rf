from flask import Flask, render_template, request, redirect, url_for, session, flash
import sqlite3, re, os
from werkzeug.security import generate_password_hash, check_password_hash
from functools import wraps
from datetime import date

BASE = os.path.dirname(os.path.abspath(__file__))
app = Flask(__name__)
app.secret_key = os.environ.get('SECRET_KEY', 'change-this-demo-secret-key')
DB = os.path.join(BASE, 'conference.db')
STATUSES = ['Новая', 'Мероприятие назначено', 'Завершено']
ROOMS = ['Аудитория', 'Коворкинг', 'Кинозал']
PAYMENTS = ['При очном посещении', 'Перевод по СБП']

def db():
    con = sqlite3.connect(DB)
    con.row_factory = sqlite3.Row
    con.execute('PRAGMA foreign_keys=ON')
    return con

def init_db():
    with db() as con:
        con.executescript('''
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            login TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            full_name TEXT NOT NULL,
            phone TEXT NOT NULL,
            email TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            room TEXT NOT NULL CHECK(room IN ('Аудитория','Коворкинг','Кинозал')),
            event_date TEXT NOT NULL,
            payment TEXT NOT NULL CHECK(payment IN ('При очном посещении','Перевод по СБП')),
            status TEXT NOT NULL DEFAULT 'Новая',
            review TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
        CREATE INDEX IF NOT EXISTS idx_bookings_user ON bookings(user_id);
        CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status);
        ''')

def logged_in(f):
    @wraps(f)
    def inner(*a, **kw):
        if not session.get('user_id') and not session.get('is_admin'):
            flash('Сначала войдите в систему.', 'error')
            return redirect(url_for('login'))
        return f(*a, **kw)
    return inner

def admin_only(f):
    @wraps(f)
    def inner(*a, **kw):
        if not session.get('is_admin'):
            flash('Доступ только для администратора.', 'error')
            return redirect(url_for('login'))
        return f(*a, **kw)
    return inner

@app.context_processor
def shared():
    return {'statuses': STATUSES, 'rooms': ROOMS, 'payments': PAYMENTS}

@app.route('/')
def home():
    return render_template('home.html')

@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        login = request.form.get('login', '').strip()
        password = request.form.get('password', '')
        full_name = request.form.get('full_name', '').strip()
        phone = request.form.get('phone', '').strip()
        email = request.form.get('email', '').strip()
        errors = []
        if not re.fullmatch(r'[A-Za-z0-9]{6,}', login):
            errors.append('Логин: только латиница и цифры, минимум 6 символов.')
        if len(password) < 8:
            errors.append('Пароль должен содержать минимум 8 символов.')
        if not re.fullmatch(r'[А-Яа-яЁё ]{5,}', full_name):
            errors.append('ФИО: используйте кириллицу и пробелы.')
        if not re.fullmatch(r'8\(\d{3}\)\d{3}-\d{2}-\d{2}', phone):
            errors.append('Телефон должен быть в формате 8(XXX)XXX-XX-XX.')
        if not re.fullmatch(r'[^\s@]+@[^\s@]+\.[^\s@]+', email):
            errors.append('Введите корректный адрес электронной почты.')
        if errors:
            for error in errors:
                flash(error, 'error')
            return render_template('register.html', values=request.form)
        try:
            with db() as con:
                con.execute(
                    'INSERT INTO users(login,password_hash,full_name,phone,email) VALUES(?,?,?,?,?)',
                    (login, generate_password_hash(password), full_name, phone, email)
                )
            flash('Аккаунт создан. Теперь войдите.', 'success')
            return redirect(url_for('login'))
        except sqlite3.IntegrityError:
            flash('Такой логин уже занят. Выберите другой.', 'error')
    return render_template('register.html', values={})

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        login = request.form.get('login', '').strip()
        password = request.form.get('password', '')
        if login == 'Conf2027' and password == 'Demo77':
            session.clear()
            session['is_admin'] = True
            session['admin_login'] = login
            return redirect(url_for('admin'))
        with db() as con:
            user = con.execute('SELECT * FROM users WHERE login=?', (login,)).fetchone()
        if user and check_password_hash(user['password_hash'], password):
            session.clear()
            session['user_id'] = user['id']
            session['user_name'] = user['full_name']
            return redirect(url_for('dashboard'))
        flash('Неверный логин или пароль.', 'error')
    return render_template('login.html')

@app.route('/logout')
def logout():
    session.clear()
    flash('Вы вышли из системы.', 'success')
    return redirect(url_for('home'))

@app.route('/dashboard')
@logged_in
def dashboard():
    if session.get('is_admin'):
        return redirect(url_for('admin'))
    with db() as con:
        bookings = con.execute(
            'SELECT * FROM bookings WHERE user_id=? ORDER BY id DESC',
            (session['user_id'],)
        ).fetchall()
    return render_template('dashboard.html', bookings=bookings)

@app.route('/booking', methods=['GET', 'POST'])
@logged_in
def booking():
    if session.get('is_admin'):
        return redirect(url_for('admin'))
    if request.method == 'POST':
        room = request.form.get('room')
        event_date = request.form.get('event_date')
        payment = request.form.get('payment')
        if room not in ROOMS or payment not in PAYMENTS or not event_date:
            flash('Заполните все поля корректно.', 'error')
        elif event_date < date.today().isoformat():
            flash('Дата мероприятия не может быть в прошлом.', 'error')
        else:
            with db() as con:
                con.execute(
                    'INSERT INTO bookings(user_id,room,event_date,payment) VALUES(?,?,?,?)',
                    (session['user_id'], room, event_date, payment)
                )
            flash('Заявка отправлена администратору. Статус: «Новая».', 'success')
            return redirect(url_for('dashboard'))
    return render_template('booking.html', today=date.today().isoformat())

@app.route('/review/<int:booking_id>', methods=['POST'])
@logged_in
def review(booking_id):
    if session.get('is_admin'):
        return redirect(url_for('admin'))
    value = request.form.get('review', '').strip()
    if not value or len(value) > 1000:
        flash('Отзыв должен содержать от 1 до 1000 символов.', 'error')
    else:
        with db() as con:
            row = con.execute(
                'SELECT status FROM bookings WHERE id=? AND user_id=?',
                (booking_id, session['user_id'])
            ).fetchone()
            if row and row['status'] == 'Завершено':
                con.execute(
                    'UPDATE bookings SET review=? WHERE id=? AND user_id=?',
                    (value, booking_id, session['user_id'])
                )
                flash('Спасибо за отзыв!', 'success')
            else:
                flash('Отзыв доступен только после завершения мероприятия.', 'error')
    return redirect(url_for('dashboard'))

@app.route('/admin', methods=['GET', 'POST'])
@admin_only
def admin():
    if request.method == 'POST':
        booking_id = request.form.get('booking_id', '')
        status = request.form.get('status', '')
        if status in STATUSES:
            with db() as con:
                con.execute('UPDATE bookings SET status=? WHERE id=?', (status, booking_id))
            flash('Статус заявки обновлён.', 'success')
        return redirect(url_for(
            'admin', q=request.args.get('q', ''), status=request.args.get('status', '')
        ))
    query = request.args.get('q', '').strip()
    status_filter = request.args.get('status', '')
    sql = 'SELECT b.*,u.full_name,u.login,u.email FROM bookings b JOIN users u ON u.id=b.user_id WHERE 1=1'
    params = []
    if query:
        sql += ' AND (u.full_name LIKE ? OR u.login LIKE ? OR b.room LIKE ?)'
        params += [f'%{query}%'] * 3
    if status_filter in STATUSES:
        sql += ' AND b.status=?'
        params.append(status_filter)
    sql += ' ORDER BY b.id DESC'
    with db() as con:
        bookings = con.execute(sql, params).fetchall()
    return render_template('admin.html', bookings=bookings, q=query, status_filter=status_filter)

if __name__ == '__main__':
    init_db()
    app.run(debug=True)
