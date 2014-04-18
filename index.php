<?php

require_once 'lib/SocialAuther/autoload.php';
require_once 'config.inc.php';

$adapterConfigs = array(
    'vk' => array(
        'client_id'     => '4108869',
        'client_secret' => 'QSVF0ak5yG1SZVx2vZSp',
        'redirect_uri'  => 'http://bbstudio.w.pw/test/?provider=vk'
    ),
    'google' => array(
        'client_id'     => '949458830781.apps.googleusercontent.com',
        'client_secret' => '-R9O4UaGnjqZUz1DvNqj-Zcr',
        'redirect_uri'  => 'http://bbstudio.w.pw/test/?provider=google'
    ),
    'facebook' => array(
        'client_id'     => '1440250126214851',
        'client_secret' => '0b7003cd4b52b1a965ee001c5fd492c4',
        'redirect_uri'  => 'http://bbstudio.w.pw/test/?provider=facebook'
    )
);

$adapters = array();
foreach ($adapterConfigs as $adapter => $settings) {
    $class = 'SocialAuther\Adapter\\' . ucfirst($adapter);
    $adapters[$adapter] = new $class($settings);
}

if (isset($_GET['provider']) && array_key_exists($_GET['provider'], $adapters) && !isset($_SESSION['user'])) {
    $auther = new SocialAuther\SocialAuther($adapters[$_GET['provider']]);

    if ($auther->authenticate()) {

        $result = mysql_query(
            "SELECT *  FROM `users` WHERE `provider` = '{$auther->getProvider()}' AND `social_id` = '{$auther->getSocialId()}' LIMIT 1"
        );

        $record = mysql_fetch_array($result);
        if (!$record) {
            $values = array(
                $auther->getProvider(),
                $auther->getSocialId(),
                $auther->getName(),
                $auther->getEmail(),
                $auther->getSocialPage(),
                $auther->getSex(),
                date('Y-m-d', strtotime($auther->getBirthday())),
                $auther->getAvatar()
            );

            $query = "INSERT INTO `users` (`provider`, `social_id`, `name`, `email`, `social_page`, `sex`, `birthday`, `avatar`) VALUES ('";
            $query .= implode("', '", $values) . "')";
            $result = mysql_query($query);
        } else {
            $userFromDb = new stdClass();
            $userFromDb->provider   = $record['provider'];
            $userFromDb->socialId   = $record['social_id'];
            $userFromDb->name       = $record['name'];
            $userFromDb->email      = $record['email'];
            $userFromDb->socialPage = $record['social_page'];
            $userFromDb->sex        = $record['sex'];
            $userFromDb->birthday   = date('m.d.Y', strtotime($record['birthday']));
            $userFromDb->avatar     = $record['avatar'];
        }

        $user = new stdClass();
        $user->provider   = $auther->getProvider();
        $user->socialId   = $auther->getSocialId();
        $user->name       = $auther->getName();
        $user->email      = $auther->getEmail();
        $user->socialPage = $auther->getSocialPage();
        $user->sex        = $auther->getSex();
        $user->birthday   = $auther->getBirthday();
        $user->avatar     = $auther->getAvatar();

        if (isset($userFromDb) && $userFromDb != $user) {
            $idToUpdate = $record['id'];
            $birthday = date('Y-m-d', strtotime($user->birthday));

            mysql_query(
                "UPDATE `users` SET " .
                "`social_id` = '{$user->socialId}', `name` = '{$user->name}', `email` = '{$user->email}', " .
                "`social_page` = '{$user->socialPage}', `sex` = '{$user->sex}', " .
                "`birthday` = '{$birthday}', `avatar` = '$user->avatar' " .
                "WHERE `id`='{$idToUpdate}'"
            );
        }

        $_SESSION['user'] = $user;
    }

    header("location:index.php");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
   	<meta http-equiv="Content-Type" content="application/javascript; charset=UTF-8" />
    <title></title>
</head>
<body style="width: 100%; height:100%; overflow: hidden;">

<?php
if (isset($_SESSION['user'])) {
   // echo '<p><a href="info.php">Перейти по ссылке</a></p>';
	//echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=http://bbstudio.w.pw/test/info.php">';
        echo '<div style="width: 100%; height:100%; overflow: hidden;"><iframe scrolling="no" style="position: absolute; width: 100%; height:100%; overflow: hidden;" src="info.php"></iframe></div>';
} else if (!isset($_GET['code']) && !isset($_SESSION['user'])) {
    foreach ($adapters as $title => $adapter) {
       // echo '<p><a href="' . $adapter->getAuthUrl() . '">Аутентификация через ' . ucfirst($title) . '</a></p>';
    }
    echo 'Войти с использованием: <a href="http://oauth.vk.com/authorize?client_id=4108869&scope=notify&redirect_uri=http://bbstudio.w.pw/test/?provider=vk&response_type=code"><img width=50px height=50px src="http://dedushka.org/img/upl/2013/04/25b2916b5c49db617f52fa5ea48efee7.jpg"></a> '
    . '<a href="https://accounts.google.com/o/oauth2/auth?redirect_uri=http://bbstudio.w.pw/test/?provider=google&response_type=code&client_id=949458830781.apps.googleusercontent.com&scope=https://www.googleapis.com/auth/userinfo.email%20https://www.googleapis.com/auth/userinfo.profile"><img width=50px height=50px src="http://uxus.net/wp-content/uploads/2011/12/google-+1.jpg"></a> ';
    
    echo '<div id="map" style="width: 1000px; height: 900px"></div>
		<div id="res"></div>';
    }
?>


<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
<script type="text/javascript" src="js/noty/packaged/jquery.noty.packaged.min.js"></script>

<script src="http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript"></script>

<link href="css/bootstrap.min.css" rel="stylesheet" />
<script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        
        ymaps.ready(init);

		//Определение начальных параметров карты


        function init () {

		//navigator.geolocation.getCurrentPosition(function(position) {
            //var latitude = position.coords.latitude;
           // var longitude = position.coords.longitude;  
			
		     var myMap = new ymaps.Map("map", {
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

       var myPlacemark = new ymaps.Placemark([ymaps.geolocation.latitude,ymaps.geolocation.longitude], {
					iconContent: "Вы здесь!",
                    }, {
                        preset: "twirl#redStretchyIcon"
                    });
                // Добавляем метку в коллекцию
                myMap.geoObjects.add(myPlacemark);
				
			//Запрос данных и вывод маркеров на карту
		$.getJSON("vivodpointsmap.php",
		function(json){
				for (i = 0; i < json.markers.length; i++) {

					var myPlacemark = new ymaps.Placemark([json.markers[i].lat,json.markers[i].lon], {
                    // Свойства
                    iconContent: json.markers[i].icontext, 
					hintContent: json.markers[i].hinttext,
                    balloonContentBody: json.markers[i].balloontext                   
					}, {
                    // Опции
                    preset: json.markers[i].styleplacemark					
                });

				// Добавляем метку на карту
				myMap.geoObjects.add(myPlacemark);

			}

		});	
		
		 myMap.events.add('click', function (e) {
		var n = noty({text: 'Добавление меток станет доступно после аутентификации.</a> '});
		});
		
		//});
		
        }
    </script>
		
		
</body>
</html>