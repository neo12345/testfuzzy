<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<form method="post">
<input type="submit" name="but_showData" value="Show dataset"/>
<input type="submit" name="but_addRule" value="Add rule"/>
</form>
<?php
function connectDB(){
$servername = "localhost";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=tranfusion", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
}

function selectTranfusionData(){
	$servername = "localhost";
	$username = "root";
	$password = "";
	
	$conn = new PDO("mysql:host=$servername;dbname=tranfusion", $username, $password);
    $stmt = $conn->prepare("SELECT * FROM data");
    $stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	return $stmt->fetchAll();
}

function selectTranfusionRule(){
	$servername = "localhost";
	$username = "root";
	$password = "";
	
	$conn = new PDO("mysql:host=$servername;dbname=tranfusion", $username, $password);
    $stmt = $conn->prepare("SELECT * FROM rule");
    $stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	return $stmt->fetchAll();
}


function recency($line, $rule){
		if($line['recency'] <= 4){
			$rule['recency'] = 'rat_gan_day';
			$rule['recency.fis'] = 1;
			$recency = - $line['recency'] * 0.25 + 1; //y = -0.25x + 1
			$rule['CF'] = 1 * $recency;
			if($rule['CF'] > 0.2){
			frequency($line, $rule);
			}
		}
		
		if($line['recency'] >= 0 && $line['recency'] <= 9){
			$rule['recency'] = 'gan_day';
			$rule['recency.fis'] = 2;
			if($line['recency'] <= 4){
				$recency = $line['recency'] * 0.25; //y = 0.25x
				
			}
			else{
				$recency = - $line['recency'] * 0.2 + 1.8; //y= -0.2x + 1.8
			}
			$rule['CF'] = 1 * $recency;
			if($rule['CF'] > 0.2){
			frequency($line, $rule);
			}
		}
		
		if($line['recency'] >= 4 && $line['recency'] <= 16){
			$rule['recency'] = 'trung_binh';
			$rule['recency.fis'] = 3;
			if($line['recency'] <= 9){
				$recency = $line['recency'] * 0.2 - 0.8;//y= 0.2x -0.8
			}
			else{
				$recency = -$line['recency'] * 1/7 + 16/7;//y= -1/7*x+16/7
			}
			$rule['CF'] = 1 * $recency;
			if($rule['CF'] > 0.2){
			frequency($line, $rule);
			}
		}
		
		if($line['recency'] >= 9 && $line['recency'] <= 30){
			$rule['recency'] = 'kha_lau';
			$rule['recency.fis'] = 4;
			if($line['recency'] <= 16){
				$recency = $line['recency'] * 1/7 -9/7;//y= 1/7*x -9/7
			}
			else{
				$recency = -$line['recency']/14 + 15/7;//y= -x/14 +15/7
			}
			$rule['CF'] = 1 * $recency;
			if($rule['CF'] > 0.2){
			frequency($line, $rule);
			}
		}
		
		if($line['recency'] >= 16){
			$rule['recency'] = 'rat_lau';
			$rule['recency.fis'] = 5;
			if($line['recency'] <= 30){
				$recency = $line['recency']/14- 8/7;//y= x/14 -8/7
			}
			else{
				$recency = 1;
			}
			$rule['CF'] = 1 * $recency;
			if($rule['CF'] > 0.2){
			frequency($line, $rule);
			};
		}
}

function frequency($line, $rule){
		if($line['frequency'] <= 3){
			$rule['frequency'] = 'rat_hiem';
			$rule['frequency.fis'] = 1;
			$frequency = -0.5 * $line['frequency'] + 1.5;//y= -0.5*x +1.5
			$rule['CF'] = $rule['CF'] * $frequency;
			if($rule['CF'] > 0.2){
			monetary($line, $rule);
			}
		}
		
		if($line['frequency'] >= 1 && $line['frequency'] <= 5){
			$rule['frequency'] = 'hiem';
			$rule['frequency.fis'] = 2;
			if($line['frequency']<=3){
				$frequency = 0.5 * $line['frequency'] - 0.5;//y= 0.5*x -0.5
			}
			else{
				$frequency = -0.5 * $line['frequency'] + 2.5;//y= -0.5*x +2.5
			}
			$rule['CF'] = $rule['CF'] * $frequency;
			if($rule['CF'] > 0.2){
			monetary($line, $rule);
			}
		}
		
		if($line['frequency'] >= 3 && $line['frequency'] <= 10){
			$rule['frequency'] = 'trung_binh';
			$rule['frequency.fis'] = 3;
			if($line['frequency']<=5){
				$frequency = 0.5 * $line['frequency'] - 1.5;//y= 0.5*x -1.5
			}
			else{
				$frequency = -0.2 * $line['frequency'] + 2;//y= -0.2*x +2
			}
			$rule['CF'] = $rule['CF'] * $frequency;
			if($rule['CF'] > 0.2){
			monetary($line, $rule);
			}
		}
		
		if($line['frequency'] >= 5 && $line['frequency'] <= 30){
			$rule['frequency'] = 'nhieu';
			$rule['frequency.fis'] = 4;
			if($line['frequency']<=10){
				$frequency = 0.2 * $line['frequency'] - 1;//y= 0.2*x-1;
			}
			else{
				$frequency = -0.05 * $line['frequency'] + 1.5;//y= -0.05*x+1.5
			}
			$rule['CF'] = $rule['CF'] * $frequency;
			if($rule['CF'] > 0.2){
			monetary($line, $rule);
			}
		}
		
		if($line['frequency'] >= 10){
			$rule['frequency'] = 'rat_nhieu';
			$rule['frequency.fis'] = 5;
			if($line['frequency']<=30){
				$frequency = 0.05 * $line['frequency'] - 0.5;//y=0.05*x-0.5
			}
			else{
				$frequency = 1;
			}
			$rule['CF'] = $rule['CF'] * $frequency;
			if($rule['CF'] > 0.2){
			monetary($line, $rule);
			}
		}
}

function monetary($line, $rule){
		if($line['monetary'] <= 500){
			$rule['monetary'] = 'rat_it';
			$rule['monetary.fis'] = 1;
			$monetary = -$line['monetary']/250 + 2;//y= -1/250*x+2
			$rule['CF'] = $rule['CF'] * $monetary;
			if($rule['CF'] > 0.2){
			times($line, $rule);
			}
		}
		
		if($line['monetary'] >= 250 && $line['monetary'] <= 1000){
			$rule['monetary'] = 'it';
			$rule['monetary.fis'] = 2;
			if($line['monetary']<=500){
				$monetary = $line['monetary']/250 -1;//y=x/250-1
			}
			else{
				$monetary = -$line['monetary']/500 + 2;//y=-x/500+2
			}
			$rule['CF'] = $rule['CF'] * $monetary;
			if($rule['CF'] > 0.2){
			times($line, $rule);
			}
		}
		
		if($line['monetary'] >= 500 && $line['monetary'] <= 2000){
			$rule['monetary'] = 'trung_binh';
			$rule['monetary.fis'] = 3;
			if($line['monetary']<=1000){
				$monetary = $line['monetary'] / 500 - 1;//y=x/5000 -1
			}
			else{
				$monetary = -$line['monetary'] / 1000 + 2;//y=-x/1000 +2
			}
			$rule['CF'] = $rule['CF'] * $monetary;
			if($rule['CF'] > 0.2){
			times($line, $rule);
			}
		}
		
		if($line['monetary'] >= 1000 && $line['monetary'] <= 6000){
			$rule['monetary'] = 'nhieu';
			$rule['monetary.fis'] = 4;
			if($line['monetary']<=2000){
				$monetary = $line['monetary'] / 1000 - 1;//y=x/1000 -1
			}
			else{
				$monetary = -$line['monetary'] / 4000 + 1.5;//y=-x/4000 +1.5
			}
			$rule['CF'] = $rule['CF'] * $monetary;
			if($rule['CF'] > 0.2){
			times($line, $rule);
			}
		}
		
		if($line['monetary'] >= 2000){
			$rule['monetary'] = 'rat_nhieu';
			$rule['monetary.fis'] = 5;
			if($line['monetary'] <= 6000){
				$monetary = $line['monetary']/4000 - 0.5;//y=x/4000-0.5
			}
			else{
				$monetary = 1;
			}
			$rule['CF'] = $rule['CF'] * $monetary;
			if($rule['CF'] > 0.2){
			times($line, $rule);
			}
		}
}

function times($line, $rule){
		if($line['time'] <= 16){
			$rule['time'] = 'rat_moi';
			$rule['time.fis'] = 1;
			$time = -$line['time']/14 + 8/7;//y=-x/14+8/7
			$rule['CF'] = $rule['CF'] * $time;
			if($rule['CF'] > 0.2){
			donated($line, $rule);
			}
		}
		
		if($line['time'] >= 0 && $line['time'] <= 30){
			$rule['time'] = 'moi';
			$rule['time.fis'] = 2;
			if($line['time']<=16){
				$time = $line['time'] / 14 -1/7;//y=x/14 -1/7
			}
			else{
				$time = -$line['time'] / 14 +15/7;//y=-x/14 +15/7
			}
			$rule['CF'] = $rule['CF'] * $time;
			if($rule['CF'] > 0.2){
			donated($line, $rule);
			}
		}
		
		if($line['time'] >= 16 && $line['time'] <= 52){
			$rule['time'] = 'trung_binh';
			$rule['time.fis'] = 3;
			if($line['time']<=30){
				$time = $line['time'] / 14 -8/7;//y=x/14 -8/7
			}
			else{
				$time = -$line['time'] / 21 +26/11;//y=-x/21 +26/11
			}
			$rule['CF'] = $rule['CF'] * $time;
			if($rule['CF'] > 0.2){
			donated($line, $rule);
			}
		}
		
		if($line['time'] >= 30 && $line['time'] <= 98){
			$rule['time'] = 'lau';
			$rule['time.fis'] = 4;
			if($line['time']<=52){
				$time = $line['time'] / 22 -15/11;//y=x/22 -15/11
			}
			else{
				$time = -$line['time'] / 46 +49/23;//y=-x/46 +49/23
			}
			$rule['CF'] = $rule['CF'] * $time;
			donated($line, $rule);
		}
		
		if($line['time'] >= 52){
			$rule['time'] = 'rat_lau';
			$rule['time.fis'] = 5;
			$time = $line['time'] / 46 -26/23;//y=x/46 -26/23;
			$rule['CF'] = $rule['CF'] * $time;
			if($rule['CF'] > 0.2){
			donated($line, $rule);
			}
		}
}

function donated($line, $rule){
	if($line['donated'] == 0){
		$rule['donated'] = 'khong_hien';
		$rule['donated.fis'] = 1;
		addRule($rule);
	}
	else{
		$rule['donated'] = 'co_hien';
		$rule['donated.fis'] = 2;
		addRule($rule);
	}
}

function addRule($rule){
	if($rule['CF'] > 0.2)
	{
		$servername = "localhost";
		$username = "root";
		$password = "";
	
		$conn = new PDO("mysql:host=$servername;dbname=tranfusion", $username, $password);
		$stmt = $conn->prepare("SELECT * FROM rule where recency = ? and frequency = ? and monetary = ? and time = ?");

		$stmt->execute(array($rule['recency'], $rule['frequency'], $rule['monetary'], $rule['time']));
		
		$oldRule = $stmt->fetchAll();
		
		if(count($oldRule) > 0){
			foreach($oldRule as $old){
				if($old['CF'] < $rule['CF']){
					$stmt = $conn->prepare("UPDATE rule SET donated = ?, CF= ? where id = ?");

					// execute the query

					$stmt->execute(array($rule['donated'], $rule['CF'], $old['id']));
					
					$stmt = $conn->prepare("UPDATE rulefis SET donated = ?, CF= ? where recency = ? and frequency = ? and monetary = ? and time = ?");
					$stmt->execute(array($rule['donated.fis'], $rule['CF'], $rule['recency.fis'], $rule['frequency.fis'], $rule['monetary.fis'], $rule['time.fis']));
				}
			}
		}
		else{
		//add newRule
					$stmt = $conn->prepare("INSERT INTO rule (recency, frequency, monetary, time, donated, CF)
					VALUES(?, ?, ?, ?, ?, ?)");

					$stmt->execute(array($rule['recency'], $rule['frequency'], $rule['monetary'], $rule['time'], $rule['donated'], $rule['CF']));
					
					
					$stmt = $conn->prepare("INSERT INTO rulefis (recency, frequency, monetary, time, donated, CF)
					VALUES(?, ?, ?, ?, ?, ?)");

					$stmt->execute(array($rule['recency.fis'], $rule['frequency.fis'], $rule['monetary.fis'], $rule['time.fis'], $rule['donated.fis'], $rule['CF']));
		}
	}
}

connectDB();
if(!empty($_POST['but_addRule'])){

$data = selectTranfusionData();
foreach($data as $line){
	$rule = array();
	recency($line, $rule);
}
$rule = selectTranfusionRule();
?>
	<table border="1">
		<th width="100px">recency</th>
		<th width="100px">frequency</th>
		<th width="100px">monetary</th>
		<th width="100px">time</th>
		<th width="100px">donated</th>
		<th width="100px">CF</th>
		<?php foreach($rule as $line){?>
		<tr>
			<td><?php echo $line['recency'];?></td>
			<td><?php echo $line['frequency'];?></td>
			<td><?php echo $line['monetary'];?></td>
			<td><?php echo $line['time'];?></td>
			<td><?php echo $line['donated'];?></td>
			<td><?php echo $line['CF'];?></td>
		</td>
		<?php } ?>
	</table>
<?php
}


if(!empty($_POST['but_showData'])){
$data = selectTranfusionData();
?>
	<table border="1">
		<th width="50px">id</th>
		<th width="100px">recency</th>
		<th width="100px">frequency</th>
		<th width="100px">monetary</th>
		<th width="100px">time</th>
		<th width="100px">donated</th>
		<?php foreach($data as $line){?>
		<tr>
			<td><?php echo $line['id'];?></td>
			<td><?php echo $line['recency'];?></td>
			<td><?php echo $line['frequency'];?></td>
			<td><?php echo $line['monetary'];?></td>
			<td><?php echo $line['time'];?></td>
			<td><?php echo $line['donated'];?></td>
		</tr>
		<?php } ?>
	</table>
<?php } 

if(empty($_POST['but_showData']) && empty($_POST['but_addRule'])){
$data = selectTranfusionData();
?>
	<table border="1">
		<th width="50px">id</th>
		<th width="100px">recency</th>
		<th width="100px">frequency</th>
		<th width="100px">monetary</th>
		<th width="100px">time</th>
		<th width="100px">donated</th>
		<?php foreach($data as $line){?>
		<tr>
			<td><?php echo $line['id'];?></td>
			<td><?php echo $line['recency'];?></td>
			<td><?php echo $line['frequency'];?></td>
			<td><?php echo $line['monetary'];?></td>
			<td><?php echo $line['time'];?></td>
			<td><?php echo $line['donated'];?></td>
		</tr>
		<?php } ?>
</table>
<?php }?>
</body>
</html>
