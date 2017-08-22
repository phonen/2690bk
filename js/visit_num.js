(function(){
		function domen(){
			//获取当前时间 
			var myDate = new Date();
			var a = myDate.getHours();
			
			//创建基本数
			var basetime = 0;
			var basermulti = 1;
				
			//判断
			switch(a) {
					case 0:
						basetime = 1;
						basermulti = 5;
						break;
					case 1:
						basetime = 1;
						basermulti = 5;
						break;
					case 2:
						basetime = 1;
						basermulti = 5;
						break;
					case 3:
						basetime = 1;
						basermulti = 5;
						break;
					case 4:
						basetime = 1;
						basermulti = 5;
						break;
					case 5:
						basetime = 5;
						basermulti = 10;
						break;
					case 6:
						basetime = 20;
						basermulti = 5;
						break;
					case 7:
						basetime = 40;
						basermulti = 20;
						break;
					case 8:
						basetime = 200;
						basermulti = 50;
						break;
					case 9:
						basetime = 2400;
						basermulti = 500;
						break;
					case 10:
						basetime = 3000;
						basermulti = 800;
						break;
					case 11:
						basetime = 3500;
						basermulti = 700;
						break;
					case 12:
						basetime = 4000;
						basermulti = 500;
						break;
					case 13:
						basetime = 2000;
						basermulti = 50;
						break;
					case 14:
						basetime = 1000;
						basermulti = 150;
						break;
					case 15:
						basetime = 1200;
						basermulti = 400;
						break;
					case 16:
						basetime = 2000;
						basermulti = 500;
						break;
					case 17:
						basetime = 2500;
						basermulti = 300;
						break;
					case 18:
						basetime = 3000;
						basermulti = 400;
						break;
					case 19:
						basetime = 5000;
						basermulti = 600;
						break;
					case 20:
						basetime = 5000;
						basermulti = 1000;
						break;
					case 21:
						basetime = 3000;
						basermulti = 400;
						break;
					case 22:
						basetime = 1000;
						basermulti = 400;
						break;
					case 23:
						basetime = 500;
						basermulti = 100;
						break;
					case 24:
						basetime = 100;
						basermulti = 50;
						break;
				}
				
			var num = Math.floor(Math.random()*basermulti);
			$("#ip_count span").text(basetime+num);
		}
		domen();
		setInterval(domen,40000);
})()