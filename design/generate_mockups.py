from PIL import Image, ImageDraw, ImageFont
from pathlib import Path
OUT=Path(__file__).parent/'exports'; OUT.mkdir(exist_ok=True)
BG='#F6F4EF'; WHITE='#FFFFFF'; INK='#1D2B3A'; GREEN='#285779'; LIME='#E8A06A'; MUTED='#68727D'; LINE='#DDDCD5'; PALE='#E6EBED'
font_path='/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf'; bold_path='/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf'
def font(size,bold=False): return ImageFont.truetype(bold_path if bold else font_path,size)
def rounded(d,xy,r,fill,outline=None,width=1): d.rounded_rectangle(xy,radius=r,fill=fill,outline=outline,width=width)
def text(d,xy,s,size=14,color=INK,bold=False): d.text(xy,s,font=font(size,bold),fill=color)
def wrap_lines(s,n):
 words=s.split(); out=[]; line=''
 for w in words:
  if len(line)+len(w)+1>n: out.append(line); line=w
  else: line=(line+' '+w).strip()
 if line: out.append(line)
 return out
def para(d,x,y,s,size=13,color=MUTED,maxchars=46,leading=1.55):
 for line in wrap_lines(s,maxchars): text(d,(x,y),line,size,color); y+=int(size*leading)
 return y
def base(w,h,title,desktop):
 im=Image.new('RGB',(w,h),BG); d=ImageDraw.Draw(im)
 pad=54 if desktop else 22
 d.rectangle((0,0,w,76 if desktop else 62),fill=WHITE)
 rounded(d,(pad,18 if desktop else 14,pad+38,56 if desktop else 52),5,GREEN)
 text(d,(pad+11,22 if desktop else 17),'К',20 if desktop else 18,WHITE,True)
 text(d,(pad+50,25 if desktop else 20),'Конференции.РФ',17 if desktop else 14,INK,True)
 if desktop: text(d,(w-370,30),'Главная    Войти    Регистрация',12,MUTED,True)
 else: text(d,(w-120,24),'Войти  ☰',12,GREEN,True)
 return im,ImageDraw.Draw(im),pad
def make(page,device):
 desktop=device=='desktop'; w,h=(1440,1024) if desktop else (390,844); im,d,p=base(w,h,page,desktop); inner=w-2*p; y=110 if desktop else 84
 def eyebrow(s):
  nonlocal y; text(d,(p,y),s.upper(),10,GREEN,True); y+=24
 def heading(s,size=None):
  nonlocal y; size=size or (42 if desktop else 29); lines=wrap_lines(s,32 if desktop else 21)
  for line in lines: text(d,(p,y),line,size,INK,True); y+=size+7
 def body(s,maxc=None):
  nonlocal y; y=para(d,p,y,s,14 if desktop else 12,MUTED,maxc or (72 if desktop else 36)); y+=18
 def card(x,y,wid,hei): rounded(d,(x,y,x+wid,y+hei),6,WHITE,LINE)
 def button(x,y,label,wid=None,secondary=False):
  wid=wid or (250 if desktop else inner); ht=46 if desktop else 44
  rounded(d,(x,y,x+wid,y+ht),5,WHITE if secondary else GREEN,LINE if secondary else None)
  text(d,(x+16,y+14),label,12,INK if secondary else WHITE,True); return y+ht
 def field(x,y,wid,label,value='',height=45):
  text(d,(x,y),label,11,INK,True); rounded(d,(x,y+19,x+wid,y+19+height),4,'#FFFFFF',LINE)
  if value: text(d,(x+12,y+32),value,11,MUTED)
  return y+height+34
 if page=='welcome':
  eyebrow('Бронирование площадок'); heading('Подберите зал для вашего события.'); body('Подберите площадку для конференции, отправьте заявку онлайн и следите за её статусом.',70 if desktop else 35)
  button(p,y,'Оформить заявку  →',280 if desktop else inner); y+=58
  button(p,y,'Войти в аккаунт',210 if desktop else inner,True); y+=75
  eyebrow('Выберите формат'); rooms=['Аудитория','Коворкинг','Кинозал']; gap=14; cw=(inner-gap*2)//3 if desktop else inner
  for i,r in enumerate(rooms):
   x=p+(i*(cw+gap) if desktop else 0); cy=y+(0 if desktop else i*100); card(x,cy,cw,86 if desktop else 82)
   rounded(d,(x+10,cy+10,x+65,cy+65),4,PALE); text(d,(x+78,cy+18),r,13,INK,True); text(d,(x+78,cy+42),'Для ваших событий',10,MUTED)
  y+=110 if desktop else 320
  eyebrow('Как это работает'); body('Выберите помещение · Дождитесь ответа · Оставьте отзыв')
 elif page=='login':
  eyebrow('С возвращением'); heading('Войти в аккаунт'); body('Введите данные, указанные при регистрации.')
  card(p,y,inner,390 if not desktop else 430); x=p+20; yy=y+25; ww=inner-40
  yy=field(x,yy,ww,'Логин','Например, Ivan2026'); yy=field(x,yy,ww,'Пароль','••••••••'); button(x,yy+4,'Войти  →',ww); text(d,(x,yy+68),'Ещё не зарегистрированы? Регистрация',11,GREEN,True); text(d,(x,yy+115),'Администратор: Conf2027 / Demo77',10,MUTED)
 elif page=='register':
  eyebrow('Новый аккаунт'); heading('Создать аккаунт'); body('Все поля обязательны для заполнения.')
  card(p,y,inner,650 if not desktop else 700); x=p+18; yy=y+20; ww=inner-36
  for lab,val in [('Логин','Латиница и цифры, от 6 символов'),('Пароль','Не менее 8 символов'),('ФИО','Иванов Иван Иванович'),('Телефон','8(999)123-45-67'),('Электронная почта','name@example.ru')]: yy=field(x,yy,ww,lab,val,height=37 if not desktop else 42)
  button(x,yy+4,'Зарегистрироваться  →',ww); text(d,(x,yy+62),'Уже есть аккаунт? Войти',11,GREEN,True)
 elif page=='booking':
  eyebrow('Бронирование · Шаг 1 из 1'); heading('Создать заявку'); body('Расскажите, какое пространство и когда вам нужно.')
  card(p,y,inner,590 if not desktop else 620); x=p+18; yy=y+20; ww=inner-36
  yy=field(x,yy,ww,'Тип помещения','Выберите формат  ⌄'); yy=field(x,yy,ww,'Желаемая дата','ДД.ММ.ГГГГ  ▦')
  text(d,(x,yy),'Способ оплаты',11,INK,True); yy+=26
  for val in ['При очном посещении','Перевод по СБП']:
   rounded(d,(x,yy,x+ww,yy+48),4,'#FAFAF8',LINE); text(d,(x+12,yy+15),'◯   '+val,11,INK,True); yy+=57
  button(x,yy+8,'Отправить заявку  →',ww)
 elif page=='dashboard':
  eyebrow('Личный кабинет'); heading('Мои заявки'); body('Здесь собраны ваши заявки и их текущие статусы.')
  button(p,y,'+ Новая заявка',200 if desktop else inner); y+=65
  for i,(room,st) in enumerate([('Аудитория','Новая'),('Коворкинг','Мероприятие назначено')]):
   card(p,y,inner,155); text(d,(p+18,y+18),f'ЗАЯВКА №{i+1:04d}',10,MUTED,True); rounded(d,(w-p-140,y+12,w-p-16,y+38),13,PALE); text(d,(w-p-130,y+19),st,9,GREEN,True); text(d,(p+18,y+56),room,16,INK,True); text(d,(p+18,y+86),'Дата: 20.11.2026',11,MUTED); text(d,(p+18,y+110),'Оплата: перевод по СБП',10,MUTED); y+=170
 elif page=='admin':
  eyebrow('Управление порталом'); heading('Заявки пользователей'); body('Поиск, фильтрация и изменение статусов.')
  card(p,y,inner,100 if not desktop else 75); text(d,(p+14,y+15),'Поиск по ФИО / логину',10,MUTED,True); rounded(d,(p+14,y+35,p+inner-14,y+72),8,'#FBFCFA',LINE); text(d,(p+25,y+46),'Введите запрос…',10,MUTED); y+=115 if not desktop else 90
  for i,(name,room,status) in enumerate([('Иванов Иван Иванович','Аудитория','Новая'),('Петрова Мария Сергеевна','Коворкинг','Мероприятие назначено')]):
   card(p,y,inner,205 if not desktop else 175); text(d,(p+16,y+16),f'ЗАЯВКА №{i+1:04d}',10,MUTED,True); text(d,(p+16,y+42),name,13,INK,True); text(d,(p+16,y+68),room+' · 20.11.2026',11,MUTED); rounded(d,(p+16,y+98,p+inner-16,y+137),4,PALE); text(d,(p+27,y+111),'Статус: '+status,10,GREEN,True); button(p+16,y+151,'Изменить статус  ⌄',inner-32,True); y+=220 if not desktop else 190
 d.line((p,h-48,w-p,h-48),fill=LINE,width=1); text(d,(p,h-34),'Конференции.РФ  ·  Пространство для важных идей',9,MUTED)
 name={'welcome':'01_welcome','login':'02_login','register':'03_register','booking':'04_booking','dashboard':'05_dashboard','admin':'06_admin'}[page]
 im.save(OUT/f'{name}_{device}_{w}x{h}.png',optimize=True)
for page in ['welcome','login','register','booking','dashboard','admin']:
 for device in ['mobile','desktop']: make(page,device)
print('Generated',len(list(OUT.glob('*.png'))),'mockups')
