<?php 


		if (isset($_GET['controller'])&&isset($_GET['action'])) {
		
			$controller=$_GET['controller'];
			$action=$_GET['action'];
		}else{
			$controller='Login';
			$action='show';
		}
		require_once('Views/Layouts/layout.php');
		


		
 ?>