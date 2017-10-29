<?php

error_reporting(-1);
header('Content-Type: text/html; charset=utf-8');
session_start();

$status = '';
$manual = '';
$message = '';

if(isset($_POST['step'])) {
	
	$_SESSION['time']++;
	
	if($_SESSION['height'] > 0) {
						
		if($_SESSION['fuel'] >= 0) {
			
			if($_POST['burn'] > 30) {
				$_POST['burn'] = 30;
			} 
			if ($_POST['burn'] < 0) {
				$_POST['burn'] = 0;
			}
			if ($_POST['burn'] > $_SESSION['fuel']) {
				$_POST['burn'] = $_SESSION['fuel'];
			}
			
			$_SESSION['fuel'] = $_SESSION['fuel'] - $_POST['burn'];
			$_SESSION['new_speed'] = $_SESSION['speed'] - $_POST['burn'] + 5;
			$_SESSION['height'] = $_SESSION['height'] - ($_SESSION['speed'] + $_SESSION['new_speed']) * 0.5;
			$_SESSION['speed'] = $_SESSION['new_speed'];
			
			if($_SESSION['height'] <= 0) {
				
				$_SESSION['height'] = 0;

                $message = 'Прилетели!';
				
				if($_SESSION['speed'] <= 0) {
					$message .= ' <b>Поздравляем идеальная посадка!</b>';
				} elseif ($_SESSION['speed'] < 2) {
					$message .= ' <b>Небольшие повреждения!</b>';
				} elseif ($_SESSION['speed'] < 5) {
					$message .= ' <b>Вы разбились!</b>';
				} elseif ($_SESSION['speed'] >= 5) {
					$message .= ' <b>Вы создали новый кратер диаметром '.$_SESSION['speed']*14.7.' футов!</b>';
				}
			}
						
		} else {
			$_SESSION['fuel'] = 0;
		}
		
	}
			
} else {
	$_SESSION['time'] = 0;
	$_SESSION['height'] = 1000;
	$_SESSION['speed'] = 50;
	$_SESSION['fuel'] = 150;
	$manual = '
				<b>LunarLander</b>
				<p>Осуществляя посадку на Луну вы взяли ручное управление на высоте 1000 футов.<br>
				Место посадки хорошее.<br>
				Ваша скорость снижения составляет 50 футов/сек.<br>
				Остаток топлива 150 едениц.</p>
				<p>Инструкция по управлению:<br>
				1. Бортовой компьютер каждую секунду сообщает высоту, скорость и остаток топлива.<br>
				2. После каждого доклада вы вводите количество едениц толпива, которое хотите сжечь за следующую секунду. Каждая еденица топлива замедляет спуск на 1 фут/сек.<br>
				3. Максимальная тяга двигателя составляет 30 футов/сек или 30 едениц топлива в секунду.<br>
				4. При контакте с лунной поверхностью двигатель автоматически выключится и вам будет предоставлен отчет о посадочной скорости и остатке топлива.<br>
				5. Если закончится топливо, вам больше не будет предлогатья ввод сжигаемого топлива, тем не менее доклады бортового компьютера продолжатся пока вы не приземлитесь.<br>
				</p>
				<p>Начало процедуры посадки!</p>
	';
	$_SESSION['table'] = '';
	$_SESSION['table'] .= '
										
						<tr>
							<td style="border: 1px solid; border-color: #000000;">Время</td>
							<td style="border: 1px solid; border-color: #000000;">Высота</td>
							<td style="border: 1px solid; border-color: #000000;">Скорость</td>
							<td style="border: 1px solid; border-color: #000000;">Горючее</td>
						</tr>
					
					
						';

}

$status = "<br><b>Статус корабля:</b><br>
					Время: ".$_SESSION['time']." секнуд<br>
					Высота: ".$_SESSION['height']." футов<br>
					Скорость: ".$_SESSION['speed']." фут/сек<br>
					Топливо: ".$_SESSION['fuel']." едениц
			";

$_SESSION['table'] .= '
										
						<tr>
							<td style="border: 1px solid; border-color: #000000;">'.$_SESSION['time'].'</td>
							<td style="border: 1px solid; border-color: #000000;">'.$_SESSION['height'].'</td>
							<td style="border: 1px solid; border-color: #000000;">'.$_SESSION['speed'].'</td>
							<td style="border: 1px solid; border-color: #000000;">'.$_SESSION['fuel'].'</td>
						</tr>
					
					
					';

@$_SESSION['graphic'] .= "[".$_SESSION['time'].", ".$_SESSION['height'].", ".$_SESSION['speed'].", ".$_SESSION['fuel']."],";

?>



<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html"; charset="utf-8">
    <title>LunarLander</title>
    
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['line']});
      google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = new google.visualization.DataTable();
      data.addColumn('number', 'Время');
      data.addColumn('number', 'Высота');
      data.addColumn('number', 'Скорость');
      data.addColumn('number', 'Горючее');

      data.addRows([
        <?php echo $_SESSION['graphic'];?>		
      ]);

      var options = {
        chart: {
          title: 'Отчет о полете:',
        },
        width: 900,
        height: 500,
        axes: {
          x: {
            0: {side: 'top'}
          }
        }
      };

      var chart = new google.charts.Line(document.getElementById('line_top_x'));

      chart.draw(data, options);
    }
  </script>

    
</head>

<body>

        <?php echo $message; ?>

        <?php echo $manual; ?>

    <div style="width: 200px; float: left;">

        <?php echo $status; ?>

        <?php if($_SESSION['height'] <= 0){ echo '<br><b>Отчет о полете:</b><table style="border: 1px solid; border-color: #000000;">'.$_SESSION['table'].'</table>'; unset($_SESSION['table']); unset($_SESSION['graphic']); echo '<div id="line_top_x"></div>';exit();}?>

        <form action="" method="post">
            <p><input type="text" name="burn"></p>
            <p><input type="submit" name="step" value="Следующий ход"></p>
        </form>
    </div>
	
    
    
    <div style="float: left;">

        
        <div style="border: 1px; height: <?php echo $_SESSION['height'] + 31;?>px; background-color: slategrey; width: 500px; float: left;">
        	<div style="border: 1px; background-color: #FF0000; width: 10px; height: <?php if($_SESSION['speed'] < 0){echo abs($_SESSION['speed']);}?>px;px; margin-left: 250px;"></div>
        	<div style="margin-left: 239px;"><img src="LunarModule.png" alt="Изображение отсутствует"></div>
            <div style="border: 1px; height: <?php echo $_SESSION['speed'];?>px; background-color: #ff0000; width: 10px; margin-left: 250px;"></div>
        </div>

        <div style="clear: both;"></div>

        <div style="border: 1px; height: 20px; width: 500px; background-color: darkslategrey;"></div>
    </div>

    <div style="clear: both;"></div>

</body>
</html>
