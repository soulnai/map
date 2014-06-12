<?php require_once 'config.inc.php'; ?>
<?php if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];  
 } else {
     header('Location: index.php');
 }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    	<meta http-equiv="Content-Type" content="application/javascript; charset=UTF-8" />
    <title></title>
	
	
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"></link>
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script> 
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="js/noty/packaged/jquery.noty.packaged.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.12.0/jquery.validate.js"></script>
<script src="http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru_UA" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.plugin.js"></script>  
<script type="text/javascript" src="js/jquery.timeentry.js"></script> 
<link href="css/jquery.timeentry.css" rel="stylesheet" />
<link href="css/bootstrap.min.css" rel="stylesheet" />

<script src="js/bootstrap.min.js"></script>

    <script type="text/javascript">
  
        ymaps.ready(init);

		//Определение начальных параметров карты
                
                
          

        function init () {
$('#map').empty();
     myMap = new ymaps.Map("map", {
                    center: [ymaps.geolocation.latitude,ymaps.geolocation.longitude], 
                    zoom: 13,
					behaviors: ["default", "scrollZoom"]
                }, {
                    balloonMaxWidth: 600
                });
			
			//Добавляем элементы управления	
			myMap.controls                
                .add('zoomControl')                
                .add('typeSelector')                
                .add('mapTools');
        
        //Коллекция для хранения меток      
       myCollection = new ymaps.GeoObjectCollection();
        clusterer = new ymaps.Clusterer({preset: 'twirl#redClusterIcons', gridSize : 32});			
			//Запрос данных и вывод маркеров на карту + ПОЯВЛЕНИЕ ВСПЛЫВАЮЩИХ МЕТОК
		
               
             renew = function () { 
               
        $.ajax({url: "vivodpointsmap.php",
            dataType : "json", 
		success: function(json){
                   
				for (i = 0; i < json.markers.length; i++) {

					var myPlacemark = new ymaps.Placemark([json.markers[i].lat,json.markers[i].lon], {
                    // Свойства
                    iconContent: json.markers[i].icontext.substring(0, 20)+'...', 
                    hintContent: json.markers[i].icontext,
                    balloonContentBody: json.markers[i].balloontext+'<br><button type="submit" class="btn btn-success" id="subscribe" aut="'+json.markers[i].author+'" game="'+json.markers[i].id+'">Сыграть</button>',   
                    author: json.markers[i].author				
					}, {
                    // Опции
                    preset: json.markers[i].styleplacemark					
                });
                                // Добавляем метку в коллекцию
                                myCollection.add(myPlacemark);
                                clusterer.add(myPlacemark);
				// Добавляем метку на карту
				//myMap.geoObjects.add(myPlacemark);
				
			};
  
		},
                error: function(){
                   var n = noty({
								layout: 'top',
                                                                text: 'Что-то случилось.',
								type        : 'error',
								dismissQueue: true,
								timeout: '5000'
					});
				
  
		}
            });
             };        
        renew ();         
       // myMap.geoObjects.add(myCollection);    
       myMap.geoObjects.add(clusterer);
           
                
    setInterval(function(){ 
        
        clusterer.removeAll();
           //  myCollection.removeAll();        
        renew (); 
         myMap.geoObjects.add(clusterer);                
       // myMap.geoObjects.add(myCollection);                
           
            }, 60000);

				
			//Отслеживаем событие клик левой кнопкой мыши на карте
            myMap.events.add('click', function (e) {
                if (!myMap.balloon.isOpen()) {
				
				    var coords = e.get('coordPosition');
					
					var myGeocoder = ymaps.geocode(coords);
myGeocoder.then(
    function (res) {
        var nearest = res.geoObjects.get(0);
        var name = nearest.properties.get('name');
		
		
		  myMap.balloon.open(coords, {  
						contentBody: '<div id="menu">\
                             <div id="menu_list"><form id="autoform">\
                                <label>Название игры:</label> <input type="text" class="input-medium" name="icon_text" id="auto" minlength="2" required/><br />\
                                 <label>День:</label> <input type="text" class="input-medium" name="date" id="datepicker" minlength="2" required/><br />\
								 <label>Время:</label> <input type="text" class="input-medium" name="time" id="time" minlength="2" value="17:00" required/><br />\
								 <label>Место:</label> <input type="text" class="input-medium" name="hint_text" value="'+name+'" minlength="2" required/><br />\
                                 <label>Комментарий:</label> <input type="text" class="input-medium" name="balloon_text"/><br />\
								 <div class="control-group"><label>Значок метки:</label>\
								 <div class="input-prepend"><span class="add-on"><img src="http://api.yandex.ru/maps/doc/jsapi/2.x/ref/images/styles/greenstr.png" style="height: 20px" /></span>\
								 <select name="image" id="image" class="span2" >\
<option data-path="http://api.yandex.ru/maps/doc/jsapi/2.x/ref/images/styles/greenstr.png" value="twirl#greenStretchyIcon">Предлагаю поиграть</option>\
<option data-path="http://api.yandex.ru/maps/doc/jsapi/2.x/ref/images/styles/redstr.png" value="twirl#redStretchyIcon">Хочу поиграть</option>\
</select></div>\
                             </div></div>\
                         <button type="submit" class="btn btn-success" id="point_submit" >Сохранить</button></form>\
                         </div>'
                                                                 });
		

					$(function() {
    var availableTags = [
      "Зіллєваріння (українське видання)",
"Подземные Короли",
"Поселенцы",
"Эадор. Владыки миров",
"Эволюция. Случайные мутации",
"Локалка",
"Активити 2 (новая версия 2013 г.)",
"Corto",
"Creatures Crossover Cyclades/Kemet (C3K)",
"Cyclades Hades",
"Northwest Passage",
"Origin",
"Takenoko",
"World of Warcraft: The Adventure Game (Мир Приключений)",
"Дум (Doom)",
"Талисман. Магическое приключение",
"Фанты Абсент (Магия желаний)",
"Фанты Взрослые забавы (Магия желаний)",
"Фанты Горячие эксперименты (Магия желаний)",
"Фанты Любовный марафон (Магия желаний)",
"Фанты. Карамельный рай (серия «Рецепты страсти»)",
"Фанты. Постельная интрижка (серия «Рецепты страсти»)",
"Fish Fish (Фиш Фиш)",
"Katamino Duo (Катамино Дуо)",
"Sequence",
"Монополия. Империя",
"Морской бой (Battleship)",
"Операция",
"Спартак (Spartacus: A Game of Blood & Treachery)",
"Поднять перископ!",
"Убонго",
"500 злобных карт",
"Имаджинариум 3D",
"Имаджинариум. Дорожно-ремонтный набор",
"Оливье",
"Абалон (видання 2013 р.)",
"Абалон: дорожня гра (видання 2013 р.)",
"Mystery of the Abbey",
"Relic Runners",
"Small World. Realms",
"Ticket to Ride - Nederlands Maps Collection",
"Мой лучший зоопарк",
"Бум Бум Балун (Boom Boom Balloon)",
"ZOOB JR. Dump Truck (Набор для самых маленьких)",
"Еволюція (українське видання)",
"Зіллєваріння (українське видання)",
"Лічильник рівнів «Манчкін-Зомбі» №1",
"Лічильник рівнів «Манчкін-Зомбі» №2",
"Лічильник рівнів «Манчкін» №1",
"Лічильник рівнів «Манчкін» №2",
"Манчкін (українською)",
"Confetti. Everlasting Party!",
"Evolution. Time to Fly (expansion)",
"Potion-Making. Guild of Alchemists (expansion)",
"Potion-Making. Practice",
"Potion-Making. University Course (expansion)",
"Shinobi. War of Clans",
"The Enigma of Leonardo",
"The Enigma of Leonardo. Quintis Fontis",
"The Kingdom of Crusaders",
"Zombie! Run for You Lives!",
"Загадка Леонардо",
"Загадка Леонардо. Дополнение Novem. 62 карты. NEW",
"Загадка Леонардо. Подарочный набор (база + Novem + Quintis Fontis)",
"Зельеварение. Подарочный набор (Практикум, УК, ГА)",
"Зельеваренье. Дополнение «Гильдия Алхимиков»",
"Зельеваренье. Дополнение «Университетский курс»",
"Конфетти. Весёлая игра для компаний",
"Космонавты (Kosmonauts)",
"Огород",
"Ордонанс",
"Ордонанс. Бустер дополнения (10 карт)",
"Подземные Короли",
"Поселенцы",
"Семейное древо",
"Синоби. Война кланов",
"Стань суперзлодеем",
"Фарт, или Приключения купца Богатеева-Пустышкина",
"Шляпа",
"Шляпа. Дополнительный набор.  Слова общей тематики.",
"Шляпа. Дополнительный набор.  Тематические подборки слов.",
"Эадор. Владыки миров",
"Эволюция. Дополнение «Время летать»",
"Эволюция. Дополнение «Континенты»",
"Эволюция. Случайные мутации",
"Я твоя понимай",
"Японский домик. 3D настольная игра",
"A Game of Thrones Board Game 2nd Edition: A Dance with Dragons",
"A Game of Thrones Board Game 2nd Edition: A Feast for Crows",
"A Game of Thrones LCG: Trial by Combat Chapter Pack",
"A Game of Thrones LCG: Valar Morgulis",
"Ad Astra",
"Adventurers: Pyramid of Horus",
"Adventurers: Pyramid of Horus - Miniatures",
"Age of Conan",
"Android",
"Arcana Card Game",
"Arkham Horror Blessed Dice Set",
"Arkham Horror: Miskatonic Horror Expansion",
"Atlanteon",
"Battles of Westeros: Brotherhood without Banners Expansion",
"Battles of Westeros: Lords of the River Expansion",
"Battles of Westeros: Tribes of  the Vale Expansion",
"Battles of Westeros: Wardens of the West Expansion",
"Battlestar Galactica: Daybreak Expansion",
"Black Gold",
"Blood Bowl Team Manager – The Card Game",
"Blood Bowl Team Manager: Sudden Death Expansion",
"Cadwallon. City of Thieves. The Kings of Ashes",
"Call of Cthulhu LCG: Ancient Horror Asylum Pack",
"Call of Cthulhu Miniatures: Cthulhu Big",
"Call of Cthulhu Miniatures: Cthulhu Small",
"Chaos in the Old World",
"Colossal Arena",
"Condottiere",
"Deadwood",
"Death Angel: Mission Pack One",
"Death Angel: Space Marine Pack One",
"Death Angel: Tyranid Enemy Pack One",
"Descent: Journeys in the Dark 2nd Edition Conversion Kit",
"Descent: Sea of Blood Expansion",
"Descent: The Labyrinth of Ruin Expansion",
"Descent: The Trollfens Expansion",
"Dragonheart",
"Elder Sign",
"Elder Sign: Unseen Forces Expansion",
"Fortress America",
"Gears of War: The Board Game – Mission Pack One",
"Horus Heresy",
"Ingenious Challenges",
"Isla Dorada",
"Kingdoms (Revised Edition)",
"Kingsburg",
"Letter of Marque",
"Lord of the Rings LCG: Nightmare Deck: Escape From Dol Goldur",
"Lord of the Rings LCG: Nightmare Deck: Journey Along the Anduin",
"Lord of the Rings LCG: Nightmare Deck: Passage Through Mirkwood",
"Lord of the Rings LCG: The Battle for Lake-town",
"Lord of the Rings LCG: The Blood of Gondor",
"Lord of the Rings LCG: The Massing at Osgiliath",
"Lord of the Rings LCG: The Morgul Vale",
"Lord of the Rings. The Card Game",
"Lord of the Rings: The Dead Marshes",
"Lord of the Rings: The Watcher in the Water",
"Mad Zeppelin",
"Mansions of Madness: House of Fears",
"Mansions of Madness: Season of the Witch",
"Mansions of Madness: The Silver Tablet",
"Mansions of Madness: The Yellow Sign",
"Mansions of Madness: Til Death Do Us Part",
"Micro Mutants: Evolution",
"Planet of Steam",
"Rune Age",
"Runebound: Mists of Zanaga Expansion",
"RuneWars (Revised Edition)",
"Sky Traders",
"Smiley Face",
"Star Wars X-Wing: Millennium Falcon",
"Talisman Revised 4th Edition: Frostmarch Expansion",
"Talisman Revised 4th Edition: Sacred Pool Expansion",
"Talisman Revised 4th Edition: The Highland Expansion",
"Talisman. The Dungeon Expansion",
"Talisman. The Reaper Expansion",
"Talisman: The Blood Moon",
"Talisman: The Dragon Expansion",
"Tannhauser",
"The Hobbit Card Game",
"Tide of Iron Campaign Expansion: Normandy",
"Tide of Iron Designer Series Vol. 1",
"Tide of Iron: Days of the Fox Expansion",
"Tide of Iron: Map Upgrade Pack One",
"Twilight Imperium 3rd Edition: Shattered Empire Expansion",
"Warhammer: Invasion LCG: Bleeding Sun Battle Pack",
"Warhammer: Invasion LCG: Burning of Dericksburg Battle Pack",
"Warhammer: Invasion LCG: Fiery Dawn Battle Pack",
"Warhammer: Invasion LCG: Karaz-a-Karak Battle Pack",
"Warhammer: Invasion LCG: March of the Damned Expansion",
"Warhammer: Invasion LCG: Omens of Ruin Battle Pack",
"Warhammer: Invasion LCG: Realm of the Phoenix King Battle Pack",
"Warhammer: Invasion LCG: Redemption of a Mage Battle Pack",
"Warhammer: Invasion LCG: Rising Dawn Battle Pack",
"Warhammer: Invasion LCG: The Chaos Moon Battle Pack",
"Warhammer: Invasion LCG: The Deathmaster's Dance Battle Pack",
"Warhammer: Invasion LCG: The Eclipse of Hope Battle Pack",
"Warhammer: Invasion LCG: The Fourth Waystone Battle Pack",
"Warhammer: Invasion LCG: The Imperial Throne Battle Pack",
"Warhammer: Invasion LCG: The Inevitable City Battle Pack",
"Warhammer: Invasion LCG: The Iron Rock Battle Pack",
"Warhammer: Invasion LCG: The Silent Forge Battle Pack",
"Warhammer: Invasion LCG: The Twin Tailed Comet Battle Pack",
"Warhammer: Invasion LCG: The Warpstone Chronicles Battle Pack",
"Warrior Knights",
"Wings of War WWI: Watch Your Back!",
"Wings of War WWII: Aichi D3A1 Val Makino/Sukida",
"Wings of War WWII: Aichi D3A1 Val Takahashi/Kozumi",
"Wings of War WWII: Aichi D3A1 Val Yamakawa/Nakata",
"Wings of War WWII: Dewoitine D520 Le Gloan",
"Wings of War WWII: Dewoitine D520 Stella",
"Wings of War WWII: Dewoitine D520 Thollon",
"Wings of War WWII: Fire from the Sky",
"Wings of War WWII: Grumman F4F-3 Martlett III Black (FAA)",
"Wings of War WWII: Grumman F4F-3 Wildcat Galer (US Marine Corps)",
"Wings of War WWII: Grumman F4F-4 Wildcat McWorther (US Navy)",
"Wings of War WWII: Hawker Hurricane Mk I Bader",
"Wings of War WWII: Hawker Hurricane Mk I Van den Hove",
"Wings of War WWII: Hawker Hurricane Mk IIb Kuznetsov",
"Wings of War WWII: Junkers Ju 87B-2 Stuka I/StG3",
"Wings of War WWII: Junkers Ju 87B-2 Stuka IV/LG1",
"Wings of War WWII: Junkers Ju 87R-2 Stuka Sugaroni",
"Wings of War WWII: Messerschmitt Bf 109 E-3 Balthasar (Luftwaffe)",
"Wings of War WWII: Messerschmitt Bf 109 E-3 Molders (Luftwaffe)",
"Wings of War WWII: Mitsubishi A6M2 Reisen Kaneko (IJNAS)",
"Wings of War WWII: Mitsubishi A6M2 Reisen Sakai (IJNAS)",
"Wings of War WWII: Mitsubishi A6M2 Reisen Shindo (IJNAS)",
"Wings of War WWII: Supermarine Spitfire Mk. I Le Mesurier (RAF)",
"World Tank Museum: Jagdpanzer IV",
"Цитаделі (українська версія)",
"Блиц День",
"Блиц Ночь",
"Голубой бриллиант",
"ЁТТА (IOTA)",
"Затерянный храм",
"Колоретто",
"Корова 006. Делюкс",
"Кот-за-хвост Цап! Делюкс",
"Мамма Мия!",
"Мафия",
"Мондо (Mondo)",
"Номы",
"Помидорный Джо",
"Джайпур (Jaipur)",
"Карамельки (Bonbons)",
"Собек (на англ.) (Sobek)",
"Хрясь! (TSCHAK!)",
"Поезд в Токио (Tokyo Train)",
"Ритм и Вызов (Rythme and Boulet)",
"Уга Буга (Ouga Bouga)",
"Фаутрак! (Foutrak!)",
"Шустрые коты (Chazz)",
"Экспромт (Speech)",
"Вертлявые червячки (Colour worm)",
"Пинги Понго (Pingi and Pongo)",
"Собери букет (Flowers matching)",
"Ягодный воришка (Catching berries)",
"Бензоколонка (Outta Gas)",
"Дядюшкина ферма (Funny Farm)",
"Интерлок (Interlock)",
"Мегаполис (Utopia)",
"Перекрёсток (Crossroads)",
"Спасите Ёжиков! (Hedgehog Escape)",
"Сырные мышки (Say Cheese)",
"Кобра-Твист (Cobra Twist)",
"Собери змею! (Cobra Cubes)",
"Аллес Пираты (Alles Kanone)",
"Аллес Томате (Alles Tomate)",
"Аллес Тролли (Alles Trolli!)",
"Банана Мачо (Banana Matcho)",
"Барабашка (Geistesblitz)",
"Барбарон спешит на свидание (Geistes Blitz 5 vor 12)",
"Вилла Палетти (Villa Paletti)",
"Ниагара (Niagara)",
"Овечья Жизнь (Haste Bock?)",
"Рифф Рафф (Riff Raff)",
"Цыплячьи бега (Zicke Zacke Huhnerkacke)",
"Червячки-огородники (Da ist der Wurm drin)",
"Бланка",
"Инве$тор 101 и 202",
"Локалка",
"Пентаго Мультиплеер",
"Перевыборы",
"Bio Trio",
"Активити - Всё возможно!",
"Активити - Обратный отсчет",
"Активити - Только для взрослых",
"Активити 1",
"Активити 2 (новая версия 2013 г.)",
"Активити Travel (компактная версия)",
"Активити для детей",
"Активити для малышей",
"Активити Тик Так Бумм",
"Актівіті українською (Activity UA)",
"Английский клуб",
"Братья акробаты",
"Кто точнее",
"Набор настольных игор XL+ шахматы+рулетка",
"Набор настольных игр 100 в 1",
"Набор настольных игр 200 в 1 + шахматы",
"Набор юного мага 100 веселых фокусов",
"Нарды (дорожная игра)",
"Разноцветные пони",
"Тик Так Бумм (компактная версия)",
"Тик Так Бумм-Вечеринка",
"Шахматы (дорожная игра)",
"Rummikub (компактная в металлической коробке)",
"Rummikub Word (Руммикуб с буквами)",
"Бой привидений",
"Больше или меньше",
"Все по местам",
"Рейс 501",
"Скоро в школу",
"Dig Mars",
"Om Nom Nom",
"Social Network",
"Будiвельнi роботи",
"Знайди тварин!",
"Король повiтря",
"Мафiозi",
"Рахуємо з ведмежатами",
"Харчовий ланцюг",
"Центральний ринок",
"Corto",
"Creatures Crossover Cyclades/Kemet (C3K)",
"Cyclades",
"Cyclades Hades",
"Kemet",
"Northwest Passage",
"Origin",
"River Dragons",
"Room 25",
"Takenoko",
"Домино дубль 6, большое, 37,8х12,4х5,3см. (Philos, Германия, арт. 3603)",
"Игра Бинг Го (Philos, Германия, арт. 3136)",
"Игра Закрыть коробку 12, 31х23х4,2см. (Philos, Германия, арт. 3120)",
"Игра Закрыть коробку 9, 28х19,5х3,2см. (Philos, Германия, арт. 3119)",
"Игра Калаха, 47х12,7х2см. (Philos, Германия, арт. 3126)",
"Игра Мельница, 29,5х14,8х5см. (Nine Men's Morris) (Philos, Германия, арт. 3135)",
"Игра Похен (Philos, Германия, арт. 3137)",
"Игра Румми, 37,5х20,5х4,5см. (Philos, Германия, арт. 3607)",
"Игра Румми, дерево, 36х20,5х5,5см. (Philos, Германия, арт. 3609)",
"Игра Румми, дерево, малая, магнитная, 22х14,5х5,8см. (Philos, Германия, арт. 3608)",
"Игра Румми, черное дерево, 36,5х20,5х5,5см. (Philos, Германия, арт. 3610)",
"Игра Солитер (Philos, Германия, арт. 3155)",
"Игра Хус, бамбук, 47,5х14х4см. (Philos, Германия, арт. 3256)",
"Игра Хус, магнитная, 47,5х14х4см. (Philos, Германия, арт. 3150)",
"Игровые элементы, 18х40 мм, 60 шт в наборе. (Philos, Германия, арт. 3051)",
"Star Wars. На грани тьмы (дополнение)",
"World of Warcraft: The Adventure Game (Мир Приключений)",
"Агрикола",
"Арена максима",
"Боец",
"Весёлая генетика. Насекомые",
"Воришки",
"ГольфМания",
"Громовой Камень",
"Да, Крёстный отец!",
"День сырка",
"Доминион. Интрига",
"Дум (Doom)",
"Заврики",
"Замес",
"Запретные слова",
"Золото",
"Зоолоретто",
"Игра Престолов НВО (по одноимённому сериалу)",
"Игра Престолов. Карточная Игра (II издание)",
"ИгроСказ",
"Инновация",
"Каркассон. Колесо Фортуны",
"Каркассон. Новые Земли",
"Ква! (Croak!)",
"Кингсбург (с дополнением «Создать Империю»)",
"Коза Ностра",
"Колонизаторы. Купцы и Варвары. Расширение для 5-6 игроков",
"Колонизаторы. Мореходы. Расширение для 5-6 игроков",
"Кондотьер",
"Королевские врата",
"Королевский Двор",
"Круг Чемпионов",
"Манчкин 4. Тяга к коняге",
"Манчкин 5. Следопуты",
"Манчкин 7. Двуручный чит",
"Манчкин 8. В хвост и в гриву",
"Мисс Русская Ночь",
"Настолье",
"Повелитель Токио",
"Правила съёма",
"Просто гениально",
"Рунебаунд (Runebound)",
"Санкт-Петербург",
"Свинтус 3D",
"Свинтус Юный",
"Сердце дракона",
"Смайлик",
"Талисман. Магическое приключение",
"Турн-и-Таксис: Королевская почта",
"Хоббит. Нежданное путешествие",
"Хроники Мутантов: Fury's Wrath",
"Хроники Мутантов: Golem of Ice",
"Хроники Мутантов: Karak's Curse",
"Хроники Мутантов: Necrotech",
"Хроники Мутантов: Striker Division",
"Хроники Мутантов: The Book Of Law",
"Хроники Мутантов: The Dark Wager",
"Хроники Мутантов: The Second Directorate",
"Хроники Мутантов: Venusian Command",
"Цари Минотавров",
"Цари скарабеев",
"Цивилизация Сида Мейера. Дополнение «Удача и слава»",
"Чарли",
"ЧёГевара",
"Энергосеть. Россия",
"Эра Конана (Age of Conan)",
"Фанты Абсент (Магия желаний)",
"Фанты Взрослые забавы (Магия желаний)",
"Фанты Горячие эксперименты (Магия желаний)",
"Фанты Любовный марафон (Магия желаний)",
"Фанты Гулянка",
"Фанты Флирт №1. Кофе-брейк",
"Фанты Флирт №2. Шуры-Муры",
"Фанты Флирт №3. Тет-а-тет",
"Фанты Флирт №4. Кофе в постель",
"Фанты Флирт №5. Туса",
"Фанты Флирт №6. Сладкая парочка",
"Фанты Флирт №7. Курортный роман",
"Фанты Флирт №8. Бутылочка",
"Фанты Флирт №9. Перчик",
"Фанты. Акробатика в кровати (серия «Рецепты страсти»)",
"Фанты. Карамельный рай (серия «Рецепты страсти»)",
"Фанты. Постельная интрижка (серия «Рецепты страсти»)",
"Batik (Батик)",
"Batik Kid (Батік для дітей)",
"Color Pop",
"Coyote (Койот)",
"Cubulus (Кубулус)",
"Fish Fish (Фиш Фиш)",
"Gobblet Kid (Гоблет дитячий)",
"Home Sweet Home",
"Kakuzu (Какузу)",
"Katamino (Катаміно)",
"Katamino DeLuxe (Катамино Люкс)",
"Katamino Duo (Катамино Дуо)",
"Katamino Pocket (Катаміно компактний)",
"Next! (Некст)",
"Ovo (Ово)",
"Papayoo (Папайя)",
"Professor Tempus (Професор Темпус)",
"Pylos (Пілос)",
"Quarto Pocket",
"Quarto! (Кварто)",
"Quarto! mini (Кварто міні)",
"Quixo (Квіксо)",
"Quixo Mini (Квіксо міні)",
"Quixo Pocket (Квіксо компактний)",
"Quoridor Kid (Коридор дитячий)",
"Quoridor Pocket (Коридор компактний)",
"Rok (Рок)",
"Sequence",
"Splash Attack (Сплеш Аттак)",
"Sputnik Kid (Супутник дитячий)",
"Stratopolis (Стратополис)",
"Tea Time (Ти Тайм)",
"Wazabi (Вазабі)",
"Yamy (Ямі)",
"5 в 1 (5 in 1)",
"UK Trivia (english)",
"Алиас Семейный (Alias Family) - рус.",
"Алиас. Скажи иначе (Alias Original)",
"Алиас. Скажи иначе Юниор (Junior Alias)",
"Биение сердца",
"Веселі Шаради",
"Весёлая Ферма (Happy Farm)",
"Весь світ",
"Володар семи земель",
"Давайте вивчати прапори Європи",
"Давайте вивчати тварин",
"Давайте вивчати цифри",
"Давайте вивчати час",
"Дивовижна Земля (Що має знати кожен про...)",
"Дикие животные",
"Дикие животные мира",
"Дотто",
"Ецци со стаканчиком (Yatzy with dice cup)",
"Європа (запитання та відповіді)",
"Калаха (Kalaha Mancala)",
"Кольорові гусениці (Разноцветные гусенички)",
"Кошенята в піжамах",
"Лексико. Учим английский язык",
"Лексико. Юниор (Lexico: Junior)",
"Лото с монстрами (Monsters Lotto)",
"Лото. Ферма (Farm Animal Lotto)",
"Лото. Фруктовое (Fruit Lotto)",
"Мемо Тварини (Memo+)",
"Мемо Транспорт (Memo+)",
"Мемо-гра Україна",
"Найцікавіші винаходи людства (Що має знати кожен про...)",
"Новем (Novem)",
"Ослик, вези!",
"Румми Классик",
"Сафари (Safari)",
"Скелеты в шкафу",
"Словарик для путешествий (Traveller's Dictionary Game)",
"Смарт 7. Дети против взрослых",
"Смарт 7. Природа и животные",
"Солитер (Solitaire)",
"Спектрум Флаги (Spectrum)",
"Спіймай кенгуру! (Kangaroo)",
"Тріволі (Trivoli)",
"Україна. Запитання та відповіді",
"Формула 1 Гран При Тачки-2 (World Grand Prix. Cars 2)",
"Фрогги на ферме / Фроггі на фермі",
"Чудеса света",
"Шахматы (Chess)",
"Шустрый мешочек (Speedybag)",
"IQ Бум",
"IQ Лінк",
"Аеропорт",
"Антивірус",
"Бек ту Бек (Back 2 Back)",
"Білл та Бетті. Цеглинки",
"Вантажівки 3",
"Вікінги. Спіймай хвилю",
"Дорожня магнітна гра. Ноїв ковчег",
"Дорожня магнітна гра. Підводний світ",
"Дорожня магнітна гра. Тангоуз: Люди",
"Дорожня магнітна гра. Тангоуз: Предмети",
"Дорожня магнітна гра. Тангоуз: Тварини",
"Дорожня магнітна гра. Чарівний ліс",
"Енгрі Бердз Андер Констракшен (Angry Birds Playground: Under Construction)",
"Енгрі Бердз он Топ (Angry Birds Playground: on Top)",
"Замок логіки",
"Зігзаг",
"Зіграємо в схованки",
"Зіграємо в схованки. Додатковий набір до гри",
"Козацькі Подорожі",
"Колір Код",
"Кролик Бу",
"Метровілле",
"Операція «Викрадач»",
"Операція «Викрадач». Додатковий набір до гри",
"Пінгвіни на льоду",
"Пірати. Сховай або знайди",
"Ріоміно",
"Сафарі. Додатковий набір до гри",
"Сафарі. Сховай або знайди",
"Троя",
"Храм-пастка",
"IQ 2х2 Гра в пари",
"IQ Сімейки",
"Англійська мова: Доміно (мовна гра)",
"Англійська мова: Мемогра (мовна гра)",
"Бумеранг",
"Ведмежата, бджілки та мед",
"Веселка (укр)",
"Вовки та вівці",
"Домино (с животными + мозаика)",
"Домино. Цвета",
"За грибами. Чудова подорож",
"Загадки-отгадки",
"К'юбікс (Qubix)",
"Квааа!",
"Краби",
"Лото Зверюшки",
"Лото. Дом",
"Лох-Несс (Loch Ness)",
"Мафия (українською)",
"Мемино Противоположности",
"Мемо Зверюшки (игра в пары)",
"Мемо Игрушки",
"Моя перша вікторина",
"Пазли Тварини",
"Пузлино. Что происходит?",
"Ранчо",
"Стук-стук",
"Суперфермер",
"Тріо",
"Унікат",
"Фигураки",
"Шнуровка (картинки для шнуровки)",
"Гольф з Метром",
"Игра в жизнь",
"Клуедо (Cluedo) (новое издание)",
"Монополия для детей. Вечеринка",
"Монополия. Дорожная игра (Monopoly Travel) (рус)",
"Монополия. Дорожная игра (Monopoly Travel) (укр)",
"Монополия. Империя",
"Монополия. Стандарт (Monopoly Standart) рус",
"Морской бой (Battleship)",
"Морской бой. Дорожняя игра (Battleship Travel)",
"Операция",
"Собери 4-ку дорожная",
"Твистер Рейв Скіп іт",
"Угадай кто? (Guess Who?)",
"Угадай кто? Дисней",
"Угадай кто? С персонажами Литл Пет Шоп",
"Угадай кто? С персонажами Тачки2",
"В поисках Немо. Океан приключений",
"Ван Хельсинг (Van Helsing)",
"Великая Отечественная. Лето 1941. Битва за Дунай",
"Винни Пух. Викторина для малышей",
"Винни Пух. Прогулка к друзьям",
"Винни Пух. Слонотоп",
"Винни Пух. Экспедиция медвежонка",
"Вуду Мания",
"Гремучие джунгли",
"Дикий, Дикий Запад",
"Дракон и Рыцари",
"Затерянные города - 2",
"Звёздные врата",
"Извилина (игра-головоломка)",
"Извилина XL (игра-головоломка)",
"Каменный век",
"Камисадо",
"Квориорс",
"Клац",
"Книга Мастеров",
"Коммерсантъ (Деньги)",
"Космические пираты",
"Космический торговец Перри Родан",
"Кто осёл?",
"Лас Вегас",
"Ледниковый период 3. Эра динозавров",
"Ледниковый период-2. Спасатели",
"Ледниковый период. Глобальное потепление",
"Летопись",
"Львиное Сердце",
"Майя",
"Микки Маус. Сокровища фараона",
"Миссия Дарвина",
"Небоскребы",
"Ой! Бежим!",
"Осторожно, осьминожки!",
"Остров обезьян",
"Панорама",
"Пираты карибского моря. На краю света",
"Пираты карибского моря. Пиратские Бароны",
"Принцессы. Королевский  бал.",
"Продвинутые шашки",
"Путешествие к центру Земли",
"Рататуй. Шеф повар Реми",
"Робин Гуд",
"Русалочка. Жемчужина морских глубин",
"Самолёты: Высший пилотаж",
"Сёгун. Битвы самураев",
"Спартак (Spartacus: A Game of Blood & Treachery)",
"Тачки: Улётные гонки",
"Терминатор: Да придёт спаситель",
"Топ Гир",
"Три мушкетёра. Подвеска королевы",
"Фауна",
"Хеллоуин",
"Хроники Нарнии. Принц Каспиан",
"Хрюшина азбука",
"Цитадель Шредера (Черепашки-ниндзя)",
"Черная молния",
"Чехарда",
"Шрек-3. Стань королем",
"Шрек-3. Школа волшебства",
"Эй! Это моя рыба!",
"Эльфийский замок",
"Буквы на Дороге",
"Классики",
"Улица Безопасная",
"Шустрики 3 в 1",
"Эрудит. Жёлтые фишки",
"Эрудит. Синие фишки",
"HIVE (Улей)",
"HIVE (Улей), дополнение Божья Коровка",
"HIVE (Улей), дополнение Мокрица",
"HIVE (Улей), дополнение Москит",
"Абракадабра",
"Айсберг",
"Алькатрас",
"Босс",
"Гарсон",
"Гипер 6",
"Данетки. Всякая всячина",
"Данетки. Деньги, власть и слава",
"Данетки. Детективные истории",
"Данетки. Случай из жизни",
"Данетки. Страсти-мордасти",
"Данетки. Юный детектив",
"Есть контакт! (Linq)",
"За Бортом. Дополнение Погода",
"Знаменосец",
"Зов Джунглей",
"Клаззл (Cluzzle)",
"Космические дальнобойщики",
"Мой любимый Франкенштейн",
"Переработка",
"Поднять перископ!",
"Ратуки",
"РобоТрок",
"Сопротивление (Resistance)",
"Стартап",
"Убонго",
"Хамелеон",
"Шакал: Остров сокровищ (дополнение)",
"Эксперимент",
"Банда умников. Траффик Джем",
"Простые Правила. Антошки",
"Простые Правила. Блин комом",
"Простые Правила. Большая Стирка",
"Простые Правила. Кошки-Мышки",
"Простые Правила. Матрешкино",
"Простые Правила. Мешочек для хранения",
"Простые Правила. Спасайся, кто может!",
"Простые Правила. Хомо Сапиенс",
"500 злобных карт",
"Имаджинариум 3D",
"Имаджинариум. Дополнительный набор «Химера»",
"Имаджинариум. Дорожно-ремонтный набор",
"Оливье",
"Castle: The Detective Card Game",
"Lord of the Rings: The Fellowship of the Ring Deck-Building Game",
"The Walking Dead Board Game",
"The Walking Dead Card Game",
"Angry Birds: На тонком льду",
"Веселі ваги",
"Стережися! Піранії!",
"Уно",
"Уно. Тачки 2  (UNO. Cars 2)",
"Уно. Школа Монстров (UNO. Monster High)",
"Ghost Stories",
"Magnifico",
"Spyrium",
"Абалон (видання 2013 р.)",
"Абалон: дорожня гра (видання 2013 р.)",
"Al Cabohne",
"Bohnanza Erweiterung-Set",
"Bohnanza Fun & Easy",
"Halli Cups",
"Halli Galli",
"Halli Klack!",
"Hugo - Das Schlossgespenst",
"La Isla Bohnita",
"Ladybohn",
"Pharaoh Code",
"Wizard Extreme",
"Wurfel Bohnanza",
"Battleship Galaxies",
"Geistertreppe",
"Kakerlakenpoker",
"Don Quixote",
"Firenze",
"Qin",
"Strasbourg",
"Yedo",
"Dominion. Intrigue",
"Ligretto - fusball (футбольный)",
"HeroCard Champion of New Olympia",
"Herocard Crab Expansion Deck",
"HeroCard Cyberspace",
"Herocard Ferrion Expansion Deck",
"HeroCard Galaxy",
"Herocard Miko Expansion Deck",
"Herocard Prince Expansion Deck",
"HeroCard Rise Of The Shogun",
"Herocard Talon Expansion Deck",
"Wealth of Nations Super Industry Tiles",
"Wealth of Nations War Clouds",
"Bugs",
"Castle Lords",
"Exalted Legacy of the Unconquered Sun BG",
"Exalted War for the Throne",
"Freitag",
"Gnome Tribes",
"Habitat",
"Hanabi",
"Herocard Egg Expansion Deck",
"Hystericoach Hockey",
"Master Builder",
"Merchants",
"Michelangelo",
"Monster Mayhem",
"Municipium",
"Roman Taxi",
"Space Pigs",
"Burn in Hell",
"Days of Steam",
"Frag Gold Edition",
"Illuminati Y2K",
"Munchkin 3 Clerical Errors Color",
"Munchkin 4 Need for Steed",
"Munchkin 6 Demented Dungeons Color",
"Munchkin Booty (Revised)",
"Munchkin Fu",
"Munchkin Fu 2 Monkey Business",
"Munchkin Go Up a Level",
"Munchkin Good Bad Munchkin Color",
"Munchkin QUEST",
"Nanuk",
"Super Munchkin",
"Super Munchkin 2 The Narrow S Cape",
"Cargo Noir",
"Cleopatra and the Society of Architects",
"Fictionaire - Pack # 4 Naturais",
"Gambit 7",
"Gang of Four",
"Memoir'44",
"Memoir'44 - Breakthrough Kit",
"Memoir'44 - Campaign Book Volume 1",
"Memoir'44 - Eastern Front",
"Memoir'44 - Mediterranean Theater",
"Memoir'44 - OP3 Battle Map - The Sword of Stalingrad/Rats in a Factory",
"Memoir'44 - OP4 Battle Map - Disaster at Dieppe/The Capture of Tobruk",
"Memoir'44 - Operation Overlord",
"Memoir'44 - Pacific Theater",
"Memoir'44 - Terrain Pack",
"Memoir'44 - Winter Wars",
"Memoir'44 - Winter/desert board map",
"Mystery of the Abbey",
"Pirate's Cove",
"Relic Runners",
"Shadows over Camelot: Card Game",
"Shadows over Camelot: Merlin's Company",
"Small World. Be Not Afraid",
"Small World. Realms",
"Small World. Tales & Legends",
"Small World. Underground",
"Ticket to Ride - Alvin & Dexter",
"Ticket to Ride - Dice Expansion",
"Ticket to Ride - Europa 1912",
"Ticket to Ride - Halloween Freighter",
"Ticket to Ride - Nederlands Maps Collection",
"Ticket to Ride - Nordic Countries",
"Ticket to Ride - USA 1910 Expansion",
"К2 (альпинистская игра)",
"Нізам",
"Airships",
"Alhambra",
"Alhambra - New York",
"Alhambra 2 The City Gates",
"Alhambra 4 The Treasure Chamber",
"Alhambra 5 The Power of Sultan",
"Alhambra The Card game",
"Chicago Express",
"Chicago Express Expansion: Narrow Gauge & Erie Railroad Company",
"Colonia",
"Fresco - Basic Game with modules 1,2,3",
"Fresco - Expansion with modules 4,5,6",
"Fresco - The Scrolls - module 7",
"German Railways",
"Granada",
"Lancaster",
"Lancaster - The New Laws Expansion",
"Mammut",
"Paris Connection",
"Robber Knights",
"Roma",
"Samarkand",
"Shogun Expansion - Tennos Court",
"Адмирал",
"Адмирал: Набор кораблей из 4-х шт для сборки",
"Зоо Регата",
"Морской бой (от Бомбат Гейм)",
"Купи слона",
"Меценат",
"Мой лучший зоопарк"
    ];
    $( "#auto" ).autocomplete({
      source: availableTags,
	  minLength: 2
    });
  });
							
                                        $('input[name="time"]').timeEntry({show24Hours: true});
					$( "#datepicker" ).datepicker({ minDate: -0, maxDate: "+1M" });
				var myPlacemark = new ymaps.Placemark(coords);
				 
				//Добавляем картинку при выборе опции select
				$('#image').change(function(){
					$('.add-on').find('img:first').attr('src', $('#image option:selected').attr('data-path'));
				});		 
				
                                   $('#autoform :input').change(function (){
                                                     $("#autoform").validate();
                                                    //$( '#point_submit' ).prop({disabled: false});
                                                    });
				//Сохраняем данные из формы		
				 $('#menu button[type="submit"]').click(function () {
                                              $("#autoform").validate();
                                              var $form = $("#autoform");
                                             
                                                var iconText = $('input[name="icon_text"]').val();
						var hintText = $('input[name="hint_text"]').val();
						var Date = $('input[name="date"]').val();
						var Time = $('input[name="time"]').val();
						var balloonText = '<b><?php echo $user->name ?> </b>предлагает поиграть в '+ $('input[name="icon_text"]').val() + '.<br><b>Когда:</b> '+ $('input[name="date"]').val()+'.<b>в</b> '+ $('input[name="time"]').val()+'.<br><b>Где:</b>'+ $('input[name="hint_text"]').val()+'<br><b>Комментарий:</b>'+$('input[name="balloon_text"]').val();
						var Author = '<?php echo $user->name ?>';
						var stylePlacemark = $('select[name=image] option:selected').val();
                                                var socialId = '<?php echo $user->socialId?>';
					
					//Передаем параметры метки скрипту addmetki.php для записи в базу данных
                                         if ($form.valid()) {
                                             $("#res").load("addmetki.php", {icontext: iconText, hinttext : hintText, date : Date, author: Author, time : Time, balloontext : balloonText, styleplacemark : stylePlacemark, lat : coords[0].toPrecision(6), lon : coords[1].toPrecision(6), socialid : socialId});
                                             //Добавляем метку на карту		
					myMap.geoObjects.add(myPlacemark);		
					//myCollection.add(myPlacemark);					
                                        clusterer.add(myPlacemark);
					//Изменяем свойства метки и балуна
			myPlacemark.properties.set({
                            iconContent: iconText.substring(0, 20),
                            hintContent: hintText,
                            balloonContent: balloonText							
                        });
						
						//Устанавливаем стиль значка метки
						myPlacemark.options.set({
							preset: stylePlacemark
						 });		
						
                        //Закрываем балун
                        myMap.balloon.close();
                                         } else {
                                            var n = noty({
								layout: 'top',
                                                                text: 'Заполните все поля, пожалуйста.',
								type        : 'error',
								dismissQueue: true,
								timeout: '5000'
					});
                                         }
                                $('#user_points').html('<img src="img\spinner_big.gif"></img>');         
                                         
				$('#user_points').empty();	
				$.getJSON("vivodusergames.php", {user: user},
		function(json){
				for (i = 0; i < json.markers.length; i++) {

					
				$( "#user_points" ).append("<div id='mygames' style='height: 35px'>"+ json.markers[i].author+" предлагает поиграть в "+json.markers[i].icontext+" в "+json.markers[i].gametime+" "+json.markers[i].gamedate+" <button type='button' class='btn btn-danger btn-sm' id='delete' data-id='"+json.markers[i].gameid+"' style='float: right; ' >delete</button></div>" );
			/*	var n = noty({
								layout: 'bottomRight',
					            text: json.markers[i].author +' хочет поиграть в '+ json.markers[i].icontext,
								type        : 'alert',
								dismissQueue: true,
								timeout: '5000'
					}); */

			}
 
		});	
					
						
						
                    });		 
						 
						 
					});		 
                    
                } else {
                    myMap.balloon.close();
                }
            });
        
        };
	
  
    $(document).ready(function() {
   
   
    user = '<?php echo $user->socialId ?>';
   
   $(document).on('click', '#subscribe', function(e) { 
       e.preventDefault();
 var $btn2 = this;  
 alert($(this).attr('id'));     
alert('Подписаны на игру '+$(this).attr('game')+' у мастера '+$(this).attr('aut'));
});
   
   
   $.ajax({url: "vivodusergames.php",
       type: "GET",
            dataType : "json", 
            data: {user: user},
		success: function(json){
                 for (i = 0; i < json.markers.length; i++) {

					
				$( "#user_points" ).append("<div id='mygames' style='height: 35px'>"+ json.markers[i].author+" предлагает поиграть в "+json.markers[i].icontext+" в "+json.markers[i].gametime+" "+json.markers[i].gamedate+" <button type='button' class='btn btn-danger btn-sm' id='delete' data-id='"+json.markers[i].gameid+"' style='float: right; ' >delete</button></div>" );
                            };  
				
  
		},
                error: function(){
                   noty({
								layout: 'top',
                                                                text: 'Что-то случилось.',
								type        : 'error',
								dismissQueue: true,
								timeout: '5000'
					});
				
  
		}
            });
   
   
   /* $.getJSON("vivodusergames.php", {user: user},
		function(json){
				for (i = 0; i < json.markers.length; i++) {

					
				$( "#user_points" ).append("<div id='mygames' style='height: 35px'>"+ json.markers[i].author+" предлагает поиграть в "+json.markers[i].icontext+" в "+json.markers[i].gametime+" "+json.markers[i].gamedate+" <button type='button' class='btn btn-danger btn-sm' id='delete' data-id='"+json.markers[i].gameid+"' style='float: right; ' >delete</button></div>" );
			/*	var n = noty({
								layout: 'bottomRight',
					            text: json.markers[i].author +' хочет поиграть в '+ json.markers[i].icontext,
								type        : 'alert',
								dismissQueue: true,
								timeout: '5000'
					}); 

			}
 
		}); */
       $(document).on('click', '#delete', function(e) {
    e.preventDefault();
    var $btn = this;
   
        
        noty({
  text: 'Do you want to continue?',
  layout: 'center',
  buttons: [
    {addClass: 'btn btn-primary', text: 'Ok', onClick: function($noty) {

        // this = button element
        // $noty = $noty element

        $noty.close();
        
        
        $($btn).closest('div').remove(); 
    
   
   
    $.getJSON("removeusergame.php", {id: $($btn).data('id')},
		function(){
				
 
		});
      // myCollection.removeAll();        
       clusterer.removeAll();        
        renew (); 
        myMap.geoObjects.add(clusterer); 
        //myMap.geoObjects.add(myCollection); 
      //  noty({text: 'You clicked "Ok" button', type: 'success'});
      }
    },
    {addClass: 'btn btn-danger', text: 'Cancel', onClick: function($noty) {
        $noty.close();
       // noty({text: 'You clicked "Cancel" button', type: 'error'});
      }
    }
  ]
});
    
           

});


    });    
    

    
    </script>

	
	
	
	
</head>
<body>
<div>
    <div id="map" style="width: 70%; height: 770px; float: left;"><img style="margin-left: auto;  margin-right: auto;" src="img\spinner_big.gif"></img></div>
<div id="res"></div>
<div style="float: right; height: 250px; width: 30%;">
<?php if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
 //   if (!is_null($user->socialId))
 //   echo "Социальный ID пользователя: " . $user->socialId . '<br />';

    if (!is_null($user->name))
    echo "Имя пользователя: " . $user->name . '<br />';
 echo '<p><a href="logout.php">Выйти из системы</a></p>';
  //  if (!is_null($user->email))
  //  echo "Email: пользователя: " . $user->email . '<br />';

  //  if (!is_null($user->socialPage))
   // echo "Ссылка на профиль пользователя: " . $user->socialPage . '<br />';

 //   if (!is_null($user->sex))
  //  echo "Пол пользователя: " . $user->sex . '<br />';

  //  if (!is_null($user->birthday))
  //  echo "День Рождения: " . $user->birthday . '<br />';

    // аватар пользователя
    if (!is_null($user->avatar))
    echo '<img width=150px height=150px src="' . $user->avatar . '" />'; echo "<br />";
   
} else {
    echo '<p><a href="index.php">Войдите в систему</a> для того, чтобы увидеть данный материал.</p>';
} ?>
    
    <a class="twitter-timeline" href="https://twitter.com/search?q=%23boardgames+%23%D0%BD%D0%B0%D1%81%D1%82%D0%BE%D0%BB%D0%BA%D0%B8" data-widget-id="456035188196143104">Tweets about "#boardgames #настолки"</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</div>
</div>

<div id="user_points" style="float: left; width: 50%"> </div>

	
</body>
</html>