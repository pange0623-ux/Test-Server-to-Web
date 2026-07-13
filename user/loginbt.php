<?php
try {
	header('X-Robots-Tag: noindex, nofollow');

	require_once __DIR__ . '/../vendor/autoload.php';

	require "../private/yarsap_8421.php";
	require "../private/yarsap_14881.php";



	$currenthost = $_SERVER['HTTP_HOST'];

	if (!isset($_SESSION)) {
		session_start();
	}





	$csrf = new CSRF($session_name = 'Logintoken_Check', $input_name = 'Logintokens_Validator', $hashTime2Live = 300, $hashSize = 64);
} catch (Exception $ex) {
	echo '<span>Something went wrong, please try again later</span>';
	logError($ex);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<meta name="googlebot" content="no index">

	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
	<title>Login Panel</title>
	<link rel="icon" type="image/png" href="https://<?php echo htmlspecialchars($currenthost); ?>/logoround.png" />

	<script src="../assets/js/pagerstranslator.js?v5"></script>



	<style>
		* {
			-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
			-webkit-tap-highlight-color: transparent;
			-webkit-user-select: none;
			-khtml-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}

		input:-webkit-autofill,
		input:-webkit-autofill:hover,
		input:-webkit-autofill:focus,
		input:-webkit-autofill:active {
			-webkit-background-clip: text;
			-webkit-text-fill-color: #ffffff;
			transition: background-color 5000s ease-in-out 0s;
			box-shadow: inset 0 0 20px 20px #00000000;
		}

		body {

			margin: 0;
			padding: 0;
			width: 100%;
			color: white;
			background-size: cover;
			background-position: center;
			background-repeat: no-repeat;
			background-attachment: fixed;
			background-color: #2D2D2D;
		}



		.navbar {
			background-color: transparent;
			color: #9458FA;
			padding: 10px;
			display: flex;
			justify-content: center;
			align-items: center;
			margin-top: 10px;
			z-index: 1;
			width: 100%;
		}




		.logo {
			max-width: 30%;
			max-height: 50%;
			margin-right: 10px;
		}



		#checkbox {
			display: none;
		}


		#loading-screen {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(13, 15, 19, 0.8);
			backdrop-filter: blur(9px);

			display: flex;
			justify-content: center;
			align-items: center;
			z-index: 9999;
			flex-direction: column;
			gap: 15px;

		}

		.spin-logo {
			width: 144px;
			height: auto;
			position: fixed;
			z-index: 1;
		}


		.spinner {
			background-image: linear-gradient(rgb(186, 66, 255) 35%,
					rgb(0, 225, 255));
			width: 144px;
			height: 144px;
			animation: spinning82341 0.9s linear infinite;
			text-align: center;
			border-radius: 50px;
			filter: blur(1px);
			box-shadow: 0px -1px 10px 0px rgb(186, 66, 255),
				0px 1px 10px 0px rgb(0, 225, 255);
		}

		.spinner1 {
			background-color: transparent;
			width: 20px;
			height: 20px;
			border-radius: 50px;
			filter: blur(10px);
		}

		@keyframes spinning82341 {
			to {
				transform: rotate(360deg);
			}
		}

		.button,
		.input,
		.select,
		.textarea {
			font: inherit;
			width: 80%;
		}

		a {
			color: inherit;
		}



		.logincontaner {

			display: flex;
			justify-content: center;
			align-items: center;
			width: 100%;
			height: 100vh;
		}


		.login-box {
			max-width: 600px;
			padding: 20px;
			background-color: #2D2D2D;
			backdrop-filter: blur(10px);
			box-sizing: border-box;

			border-radius: 10px;
			display: flex;
			flex-direction: column;
			align-items: center;
			text-align: center;
		}

		@media screen and (max-width: 720px) {
			.login-box {
				max-width: 100%;
			}
		}


		.login-box p:first-child {
			margin: 0 0 30px;
			padding: 0;
			color: #fff;
			text-align: center;
			font-size: 1.5rem;
			font-weight: bold;
			letter-spacing: 1px;
		}

		.login-box .user-box {
			position: relative;
		}

		.login-box .user-box input {
			width: 100%;
			padding: 10px 0;
			font-size: 16px;
			color: #fff;
			margin-bottom: 30px;
			border: none;
			border-bottom: 1px solid #fff;
			outline: none;
			background: transparent;
		}

		.login-box .user-box label {
			position: absolute;
			top: 0;
			left: 0;
			padding: 10px 0;
			font-size: 16px;
			color: #fff;
			pointer-events: none;
			transition: .5s;
		}

		.login-box .user-box input:focus~label,
		.login-box .user-box input:valid~label {
			top: -20px;
			left: 0;
			color: #fff;
			font-size: 12px;
		}

		.login-box form a {
			position: relative;
			display: inline-block;
			padding: 10px 20px;
			margin-top: 15px;
			font-weight: bold;
			color: #fff;
			font-size: 16px;
			text-decoration: none;
			text-transform: uppercase;
			overflow: hidden;
			transition: .5s;
			letter-spacing: 3px;
			cursor: pointer;
			background: #111;
		}

		.login-box a:hover {
			background: #111;
			color: aqua;
			border-radius: 5px;
		}

		.login-box a span {
			position: absolute;
			display: block;
		}




		.login-box a.txtlnks {
			color: aqua;
			text-decoration: none;
		}

		.login-box a.txtlnks:hover {
			background: transparent;
			color: #aaa;
			border-radius: 5px;
		}

		.login-box p {
			margin: 0;
			padding-bottom: 5px;
			padding-top: 5px;
		}

		#login-button {
			width: fit-content;
		}

		.overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: #2D2D2D;
			z-index: 9998;

		}

		.message-box {

			border-radius: 10px;
			font-size: 4vh;
			color: #fff;

			text-align: center;
			padding: 20px;
			position: fixed;

			top: 50%;

			left: 50%;

			transform: translate(-50%, -50%);

			z-index: 9999;

			width: 90%;
			gap: 20px;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
		}
		.pin-input-container {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 10px 0;
}

.pin-input {
    width: 40px;
    height: 40px;
    text-align: center;
    font-size: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.pin-input:focus {
    border-color: #666;
    outline: none;
}

.hint {
    margin-top: 10px;
    font-size: 14px;
    color: #666;
}
		.boxbutton {
			width: 100px;
			padding: 10px;
			border-radius: 15px;
			border: 0;
			background-color: white;
			box-shadow: rgb(164 0 255) 0 0 7px 2px;
			text-transform: uppercase;
			font-size: 1rem;
			transition: all 0.5s ease;
		}

		.boxbutton:hover {
			background-color: hsl(261deg 80% 48%);
			color: hsl(0, 0%, 100%);
			box-shadow: rgb(93 24 220) 0px 7px 29px 0px;
		}

		.boxbutton:active {
			background-color: hsl(261deg 80% 48%);
			color: hsl(0, 0%, 100%);
			box-shadow: rgb(93 24 220) 0px 0px 0px 0px;
			transform: translateY(10px);
			transition: 100ms;
		}

		.input {
			color: #fff;
			border: 2px solid #2cb4e3;
			border-radius: 10px;
			font-size: 1em;
			padding: 10px 25px;
			background: transparent;

			text-align: center;
		}

		.input:active {
			box-shadow: 2px 2px 15px #2cb4e3 inset;
		}


		#activation-form {
			width: 100%;
			padding: 5px;
			margin: 0 auto;
			text-align: center;
		}

		.trmsbox {
			color: #aaa;
			padding-top: 10px;
		}


		#recaptcha-first {
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.g-recaptcha {
			filter: invert(1) hue-rotate(180deg);
		}

		.language-selector {
			position: relative;
		}


		.flag-btn {
			width: 50px;
			height: 50px;
			border-radius: 50%;
			border: none;
			cursor: pointer;
			background-color: transparent;
			overflow: hidden;
		}


		.flag-icon {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}


		.dropdownflag {
			position: absolute;
			top: 39px;
			right: -15px;
			background-color: #2D2D2D;
			border: 1px solid #afafaf;
			padding: 10px;
			list-style: none;
			width: 60px;
			text-align: center;
			z-index: 3;
		}


		.hidden {
			display: none;
		}


		.dropdownflag li {
			margin: 5px 0;
			cursor: pointer;
		}

		.user-avatar img {
			width: 39px;
			height: 37px;
			border-radius: 50%;
		}


		.notifi {
			display: flex;
			position: relative;

			border: none;
			color: #2cb4e3;
			border-radius: 50%;
			width: 50px;
			height: 50px;
			cursor: pointer;
			transition: background-color 0.3s linear 0s;

			background-color: #2D2D2D;


			background-size: 200% 100%;

			align-items: center;
			justify-content: center;
		}

		@media screen and (max-width: 780px) {
			.notifi {
				width: 45px;
				height: 45px;
			}

		}

		.privtitle {
			padding: 7px;
			margin: auto 0;
			background-color: #2D2D2D;
			color: white;
			margin-bottom: 10px;
		}

		#bt-key-container {
			display: none;
			/* Hidden initially */
			background-color: #2D2D2D;
			padding: 20px;
			border-radius: 10px;
			box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
			max-width: 90%;
			width: 300px;
			text-align: center;
			color: #FFFFFF;
		}

		/* Styles for the welcome message */
		#bt-key-message {
			margin-top: 15px;
			font-size: 1.1em;
		}

		/* Styles for input and button */
		#bt-key-input {
			width: 80%;
			padding: 10px;
			margin: 10px 0;
			border: none;
			border-radius: 5px;
			background-color: #3E3E3E;
			color: #FFFFFF;
			text-align: center;
		}

		#bt-copy-button {
			background-color: #2D2D2D;
			color: #FFFFFF;
			border: none;
			padding: 10px 15px;
			cursor: pointer;
			transition: background-color 0.3s;
			border-bottom: 1px solid gray;
		}

		#bt-copy-button:hover {
			background-color: #1c1c1c;
		}

		/* Responsive styling */
		@media (max-width: 400px) {

			#bt-key-input,
			#bt-copy-button {
				width: 100%;
			}
		}

		#fanotactive {
			display: none;
			/* Hide initially; toggle display in JavaScript */
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(45, 45, 45, 0.8);
			z-index: 9999;
			justify-content: center;
			align-items: center;
		}

		/* Centered overlay content box */
		.fanotactive-content {
			background-color: #2D2D2D;
			padding: 20px;
			width: 90%;
			max-width: 400px;
			display: flex;
			color: #CCCCCC;
			border-radius: 10px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
			text-align: left;
			flex-direction: column;
			align-content: flex-start;
			align-items: center;
		}

		/* QR Image */
		#qrimg {
			max-width: 100%;
			height: auto;
			margin-bottom: 20px;
			border-radius: 8px;
		}

		/* Ordered list steps */
		ol {
			list-style: none;
			padding: 0;
			color: #CCCCCC;
		}

		ol li {
			margin: 10px 0;
			font-size: 1rem;
		}

		/* Links for app download */
		a {
			color: #CCCCCC;
			text-decoration: underline;
		}

		/* Input and button */
		#ucode {
			width: 70%;
			padding: 10px;
			margin-right: 5px;
			border: 1px solid #555;
			border-radius: 5px;
			background-color: #3A3A3A;
			color: #CCCCCC;
		}

		#confbtn {
			padding: 10px 15px;
			background-color: #5A5A5A;
			color: #CCCCCC;
			border: none;
			border-radius: 5px;
			cursor: pointer;
		}

		#confbtn:hover {
			background-color: #777;
		}

		/* Responsive adjustments */
		@media (max-width: 600px) {
			.fanotactive-content {
				width: 90%;
				padding: 15px;
			}

			#ucode {
				width: 60%;
			}

			ol li {
				font-size: 0.9rem;
			}
		}
	</style>
</head>

<body>

	
	

	<div id="csrf-token" style="display: none;">
		<?= $csrf->input('LoginForm'); ?>
	</div>

	<div class="logincontaner">
		<div class="login-box">


			<h3 class="privtitle trans_privetitle">BT-MOB</h3>

			<div class="language-selector">
				<button id="languageBtn" class="user-avatar rounded-button notifi dock-button">
					<img src="../assets/imgs/languages/US.png?v4" alt="USA Flag" id="mainFlag">
				</button>
				<ul id="dropdownflagList" class="dropdownflag hidden">
					<li data-lang="CN"><img src="../assets/imgs/languages/CN.png" alt="Chinese Flag" class="flag-icon"></li>
					<li data-lang="AR"><img src="../assets/imgs/languages/UA.png" alt="Arabic Flag" class="flag-icon"></li>
					<li data-lang="RU"><img src="../assets/imgs/languages/RU.png" alt="Russian Flag" class="flag-icon"></li>
					<li data-lang="TR"><img src="../assets/imgs/languages/TR.png" alt="turkey Flag" class="flag-icon"></li>
				</ul>
			</div>
			<form id="activation-form">

				<div class="user-box">
					<input required="" type="email" id="email" name="email">
					<label class="trans_email">Email</label>
				</div>
				<div class="user-box">
					<input required="" type="password" id="password" name="password" autocomplete="Password">
					<label class="tans_password">Password</label>
				</div>

				<div id="recaptcha-first" class="g-recaptcha"></div>
				<a class="trans_login" href="#" id="login-button"> <span></span> <span></span> <span></span> <span></span> Enter </a>
			</form>

		</div>
		<div id="bt-key-container">
			<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#e8eaed">
				<path d="M378-246 154-470l43-43 181 181 384-384 43 43-427 427Z" />
			</svg>
			<div id="bt-key-message">Welcome to BT-MOB, your Secret key</div>
			<input type="text" id="bt-key-input" readonly>
			<button id="bt-copy-button" onclick="copyKey()">Copy</button>
		</div>

		<div id="fanotactive">

			<div class="fanotactive-content">
				<img id="qrimg" src="" alt="2FA-QR">
				<h3 class="tans_2fatitle">Two-Factor Authentication (2FA)</h3>
				<ol>
					<li class="tans_2fastep1">Download Google Authenticator</li>
					<li>For <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" rel="noopener noreferrer">Android</a> or For <a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank" rel="noopener noreferrer">iOS</a></li>
					<li class="tans_2fastep2">Open Google Authenticator.</li>
					<li class="tans_2fastep3">Click the '+' sign to add a new account.</li>
					<li class="tans_2fastep4">Scan this QR code.</li>
					<li class="tans_2fastep5">Verify the setup by entering the code generated by Google Authenticator</li>
					<li style="display: flex; justify-content: center;">
						<input autocomplete="off" type="text" id="ucode" name="ucode" required placeholder="2FA-Authenticator Code">
						<button id="confbtn" class="confbtn tans_confirm">Confirm</button>
					</li>

				</ol>
			</div>
		</div>
	</div>



	<div id="toast"></div>

</body>



<script>
	const _0x5d4d70=_0xf430;(function(_0x4ffc2b,_0x31de56){const _0x380498=_0xf430,_0x3d0a67=_0x4ffc2b();while(!![]){try{const _0x1c530c=-parseInt(_0x380498(0x12d))/0x1+-parseInt(_0x380498(0xa5))/0x2+-parseInt(_0x380498(0xdc))/0x3*(-parseInt(_0x380498(0x129))/0x4)+-parseInt(_0x380498(0xce))/0x5*(parseInt(_0x380498(0xc2))/0x6)+-parseInt(_0x380498(0x123))/0x7*(-parseInt(_0x380498(0xa4))/0x8)+parseInt(_0x380498(0xf6))/0x9*(parseInt(_0x380498(0x12e))/0xa)+parseInt(_0x380498(0x112))/0xb*(-parseInt(_0x380498(0x128))/0xc);if(_0x1c530c===_0x31de56)break;else _0x3d0a67['push'](_0x3d0a67['shift']());}catch(_0x421941){_0x3d0a67['push'](_0x3d0a67['shift']());}}}(_0x3223,0x57d67));function showKeyContainer(_0x5ec837){const _0x2649cb=_0xf430;document['getElementById']('bt-key-input')[_0x2649cb(0xf7)]=_0x5ec837,document[_0x2649cb(0xd7)](_0x2649cb(0x104))[_0x2649cb(0xcc)][_0x2649cb(0xb3)]='none',document['getElementById'](_0x2649cb(0xbd))[_0x2649cb(0xcc)][_0x2649cb(0xb3)]=_0x2649cb(0xf9);}function copyKey(){const _0x3ff22c=_0xf430,_0x3ee669=document[_0x3ff22c(0xb2)]('bt-key-input');_0x3ee669[_0x3ff22c(0xcd)](),_0x3ee669[_0x3ff22c(0x114)](0x0,0x1869f),document[_0x3ff22c(0xe1)](_0x3ff22c(0xbf)),Alert_success(_0x3ff22c(0xfe)+_0x3ee669[_0x3ff22c(0xf7)]);}function _0x3223(){const _0x72a4f4=['Two-Factor\x20Authentication\x20is\x20enabled','2FA.','csrf-token','appendChild',';path=/','IMG','5000','../assets/imgs/languages/CN.png','POST','dispatchEvent','\x22\x20alt=\x22Arabic\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22RU\x22><img\x20src=\x22','then','fanotactive','69944WaNnBd','439722UeEECn','Your\x20subscription\x20has\x20expired.','#login-button','focus','swing','../assets/imgs/warning.png','className','code','\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22EN\x22><img\x20src=\x22','../assets/imgs/languages/UA.png','classList','</i>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<div\x20class=\x22button-container\x22>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<button\x20class=\x22boxbutton\x22\x20id=\x22ok-button\x22>OK\x20✓</button>\x0a\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20</div>\x0a\x20\x20\x20\x20\x20\x20\x20\x20','substring','getElementById','display','querySelectorAll','</p>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<i\x20class=\x22icon\x22>','click','Success','token','overlay','1000','activation-form','target','bt-key-container','Welcome','copy','../assets/imgs/languages/US.png?v1','src','6GfDcVF','addEventListener','300','info','location','qrimg','fadeOut','setItem','getAttribute','\x22\x20alt=\x22Arabic\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22TR\x22><img\x20src=\x22','style','select','2121940NRSzVC','confbtn','Google\x20Authenticator','email','../slowdown.php','linear','parse','\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22CN\x22><img\x20src=\x22','every','querySelector','preventDefault','keydown','Please\x20Check\x20your\x20password.','Error\x2031231','6207oMLiVj','json','Blocked','length','</div>\x0a\x20\x20\x20\x20\x20\x20\x20\x20','execCommand','../assets/imgs/languages/RU.png','\x22\x20alt=\x22Chinese\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22TR\x22><img\x20src=\x22','from','oops\x20,\x20something\x20went\x20wrong','cookie','flex','Error\x207485','../private/yarsap_13781.php','#ok-button','setTime','parentNode','add','createElement','toggle','getTime','Your\x20email\x20is\x20not\x20verified!\x20<br>\x20Please\x20verify\x20your\x20email\x20address\x20<br>\x20if\x20you\x20cannot\x20find\x20the\x20email\x20(try\x20check\x20Junk/Spam\x20folder\x20also)','mainFlag','<svg\x20xmlns=\x22http://www.w3.org/2000/svg\x22\x20height=\x22120px\x22\x20viewBox=\x220\x20-960\x20960\x20960\x22\x20width=\x22120px\x22\x20fill=\x22#666666\x22><path\x20d=\x22M445-386h70l-22-122q17-5\x2028.5-19.5T533-560q0-22-15.5-37.5T480-613q-22\x200-37.5\x2015.5T427-560q0\x2018\x2011.5\x2032.5T467-508l-22\x20122Zm35\x20252q-115-36-191.5-142T212-516v-208l268-100\x20268\x20100v208q0\x20134-76.5\x20240T480-134Zm0-30q104-33\x20172-132t68-220v-189l-240-89-240\x2089v189q0\x20121\x2068\x20220t172\x20132Zm0-315Z\x22/></svg>','\x22\x20alt=\x22Hint\x22\x20style=\x22object-fit:\x20cover;\x20max-width:\x20100%;\x20height:\x2040vh;\x22\x20/>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<p\x20class=\x22boxmsg\x22\x20>','Backspace','11691spfgRN','value','forEach','block','removeChild','success','\x22\x20alt=\x22Russian\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20','input','Copied\x20the\x20key:\x20','\x22\x20alt=\x22Turkish\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20','\x22\x20alt=\x22Chinese\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22AR\x22><img\x20src=\x22','Error','languageBtn','2FA','.login-box','div','ajax','Enter','Fail','#activation-form','selectedLanguage','VRFY','catch','innerHTML','tok','toUTCString','ucode','map','11XaEVOe','\x0a\x09\x09\x09<img\x20src=\x22','setSelectionRange','join','message-box','indexOf','test','data-lang','body','hidden','password','tagName','../private/yarsap_87911.php','href','error','</p>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<i\x20class=\x22icon\x22>','</i>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<div\x20class=\x22pin-input-container\x22>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<input\x20type=\x22text\x22\x20maxlength=\x221\x22\x20class=\x22pin-input\x22\x20/>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<input\x20type=\x22text\x22\x20maxlength=\x221\x22\x20class=\x22pin-input\x22\x20/>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<input\x20type=\x22text\x22\x20maxlength=\x221\x22\x20class=\x22pin-input\x22\x20/>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<input\x20type=\x22text\x22\x20maxlength=\x221\x22\x20class=\x22pin-input\x22\x20/>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<input\x20type=\x22text\x22\x20maxlength=\x221\x22\x20class=\x22pin-input\x22\x20/>\x0a\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20<input\x20type=\x22text\x22\x20maxlength=\x221\x22\x20class=\x22pin-input\x22\x20/>\x0a\x20\x20\x20\x20\x20\x20\x20\x20</div>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<div\x20class=\x22hint\x22>','427mPwSJi','\x22\x20alt=\x22Turkish\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22RU\x22><img\x20src=\x22','\x22\x20alt=\x22USA\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22AR\x22><img\x20src=\x22','append','Please\x20check\x20your\x20email\x20address.','1918812mMFnmT','1172XOGHyp','\x22\x20alt=\x22USA\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22CN\x22><img\x20src=\x22','../assets/imgs/languages/TR.png','<svg\x20\x20xmlns=\x22http://www.w3.org/2000/svg\x22\x20\x20width=\x2224\x22\x20\x20height=\x2224\x22\x20\x20viewBox=\x220\x200\x2024\x2024\x22\x20\x20fill=\x22none\x22\x20\x20stroke=\x22currentColor\x22\x20\x20stroke-width=\x222\x22\x20\x20stroke-linecap=\x22round\x22\x20\x20stroke-linejoin=\x22round\x22\x20\x20class=\x22icon\x20icon-tabler\x20icons-tabler-outline\x20icon-tabler-circle-dashed-check\x22><path\x20stroke=\x22none\x22\x20d=\x22M0\x200h24v24H0z\x22\x20fill=\x22none\x22/><path\x20d=\x22M8.56\x203.69a9\x209\x200\x200\x200\x20-2.92\x201.95\x22\x20/><path\x20d=\x22M3.69\x208.56a9\x209\x200\x200\x200\x20-.69\x203.44\x22\x20/><path\x20d=\x22M3.69\x2015.44a9\x209\x200\x200\x200\x201.95\x202.92\x22\x20/><path\x20d=\x22M8.56\x2020.31a9\x209\x200\x200\x200\x203.44\x20.69\x22\x20/><path\x20d=\x22M15.44\x2020.31a9\x209\x200\x200\x200\x202.92\x20-1.95\x22\x20/><path\x20d=\x22M20.31\x2015.44a9\x209\x200\x200\x200\x20.69\x20-3.44\x22\x20/><path\x20d=\x22M20.31\x208.56a9\x209\x200\x200\x200\x20-1.95\x20-2.92\x22\x20/><path\x20d=\x22M15.44\x203.69a9\x209\x200\x200\x200\x20-3.44\x20-.69\x22\x20/><path\x20d=\x22M9\x2012l2\x202l4\x20-4\x22\x20/></svg>','234108neyIPb','1990tnXKqb','Please\x20enter\x20your\x20Two-Factor\x20Authentication\x20code.'];_0x3223=function(){return _0x72a4f4;};return _0x3223();}function _0xf430(_0x75ff47,_0x281c47){const _0x322396=_0x3223();return _0xf430=function(_0xf4304c,_0x3b1124){_0xf4304c=_0xf4304c-0xa2;let _0x3a66e0=_0x322396[_0xf4304c];return _0x3a66e0;},_0xf430(_0x75ff47,_0x281c47);}var recaptchaWidgets={};function show2FAOverlay(_0x11a52d){const _0x1dea71=_0xf430;document[_0x1dea71(0xb2)](_0x1dea71(0xc7))[_0x1dea71(0xc1)]=_0x11a52d,document[_0x1dea71(0xb2)](_0x1dea71(0xa3))[_0x1dea71(0xcc)][_0x1dea71(0xb3)]=_0x1dea71(0xe7);}function validateEmail(_0x21070a){const _0x29edb9=_0xf430,_0x3a79f5=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;if(!_0x3a79f5[_0x29edb9(0x118)](_0x21070a))return _0x29edb9(0x127);return!![];}function validatePassword(_0x457dc5){const _0x30cc3f=_0xf430;if(!/(?=.*[A-Z])/[_0x30cc3f(0x118)](_0x457dc5))return'Please\x20Check\x20your\x20password';if(!/(?=.*[^a-zA-Z0-9])/[_0x30cc3f(0x118)](_0x457dc5))return _0x30cc3f(0xda);if(_0x457dc5[_0x30cc3f(0xdf)]<0x8||_0x457dc5[_0x30cc3f(0xdf)]>0x10)return'Please\x20Check\x20your\x20password.';return!![];}const activationForm=document[_0x5d4d70(0xd7)](_0x5d4d70(0x109)),loginButton=document[_0x5d4d70(0xd7)](_0x5d4d70(0xa7)),emailbox=document[_0x5d4d70(0xb2)](_0x5d4d70(0xd1)),passwordbox=document[_0x5d4d70(0xb2)](_0x5d4d70(0x11c));loginButton[_0x5d4d70(0xc3)](_0x5d4d70(0xb6),_0x8dc3a7=>{const _0x4cde30=_0x5d4d70;_0x8dc3a7[_0x4cde30(0xd8)]();let _0x512ef5=validateEmail(emailbox['value']);if(_0x512ef5!=!![]){Alert_error(_0x512ef5);return;}let _0x30b581=validatePassword(passwordbox[_0x4cde30(0xf7)]);if(_0x30b581!=!![]){Alert_error(_0x30b581);return;}const _0x219a9a=new FormData(activationForm);fetch(_0x4cde30(0xe9),{'method':'POST','body':_0x219a9a})[_0x4cde30(0xa2)](_0xb67afa=>_0xb67afa[_0x4cde30(0xdd)]())['then'](_0x59a9ba=>{const _0x502c32=_0x4cde30;if(_0x59a9ba[_0x502c32(0xde)]){window[_0x502c32(0xc6)][_0x502c32(0x11f)]=_0x502c32(0xd2);return;}else{if(_0x59a9ba[_0x502c32(0x108)]){let _0x330e94=JSON[_0x502c32(0xd4)](_0x59a9ba['Fail']);if(_0x330e94===_0x502c32(0xa6)){Alert_info(_0x502c32(0xa6));return;}else Alert_info(_0x502c32(0x101),_0x330e94);return;}else{if(_0x59a9ba['Req']){let _0x219577=JSON[_0x502c32(0xd4)](_0x59a9ba['Req']);if(_0x219577===_0x502c32(0x103))customInputBox(_0x502c32(0xf3),_0x502c32(0x12f),_0x502c32(0x131),_0x502c32(0xd0))[_0x502c32(0xa2)](function(_0xd0e4d0){const _0x26ff60=_0x502c32;_0x219a9a[_0x26ff60(0x126)]('tfacode',String(_0xd0e4d0)),fetch(_0x26ff60(0xe9),{'method':_0x26ff60(0x138),'body':_0x219a9a})[_0x26ff60(0xa2)](_0x2677e8=>_0x2677e8[_0x26ff60(0xdd)]())[_0x26ff60(0xa2)](_0x9c20ec=>{const _0x4d1401=_0x26ff60;if(_0x9c20ec['Blocked']){window[_0x4d1401(0xc6)][_0x4d1401(0x11f)]='../slowdown.php';return;}else{if(_0x9c20ec[_0x4d1401(0x108)]){let _0x3e5150=JSON['parse'](_0x9c20ec[_0x4d1401(0x108)]);if(_0x3e5150===_0x4d1401(0xa6)){Alert_info(_0x4d1401(0xa6));return;}else Alert_info('Error',_0x3e5150);return;}else{if(_0x9c20ec[_0x4d1401(0xb7)]){let _0x488b71=JSON[_0x4d1401(0xd4)](_0x9c20ec['Success']);Alert_success('Welcome',''),showKeyContainer(_0x488b71);}else return;}}})[_0x26ff60(0x10c)](_0x109a21=>{const _0x133108=_0x26ff60;console[_0x133108(0x120)](_0x133108(0xdb),_0x109a21);});});else{if(_0x219577===_0x502c32(0x10b))customMessageBox(_0x502c32(0x12c),_0x502c32(0xf1),_0x502c32(0xaa));else try{_0x219577['CTFA']==='OK'&&(document[_0x502c32(0xb2)](_0x502c32(0xcf))[_0x502c32(0xc3)](_0x502c32(0xb6),()=>Enable2FA(_0x219577['EM'],_0x219577[_0x502c32(0x10e)])),show2FAOverlay(_0x219577['QR']));}catch(_0x4787fe){console[_0x502c32(0x120)]('Error\x20parsing\x20JSON:',_0x4787fe);}}}else{if(_0x59a9ba[_0x502c32(0xb7)]){let _0x2179e6=JSON[_0x502c32(0xd4)](_0x59a9ba[_0x502c32(0xb7)]);Alert_success(_0x502c32(0xbe),''),showKeyContainer(_0x2179e6);}else return;}}}})[_0x4cde30(0x10c)](_0x3c4c11=>{const _0x13fb62=_0x4cde30;console['error'](_0x13fb62(0xe8),_0x3c4c11);});});function Enable2FA(_0x2fde8e,_0x524281){const _0x385ed4=_0x5d4d70;let _0x2aadd1=document[_0x385ed4(0xb2)](_0x385ed4(0x110))['value'];if(!_0x2aadd1||_0x2aadd1[_0x385ed4(0xdf)]===0x0){Alert_error('Enter\x20Code\x20From\x202FA\x20App.');return;}var _0x1905e9=new FormData();_0x1905e9[_0x385ed4(0x126)](_0x385ed4(0xac),_0x2aadd1),_0x1905e9[_0x385ed4(0x126)](_0x385ed4(0xd1),_0x2fde8e),_0x1905e9[_0x385ed4(0x126)](_0x385ed4(0xb8),_0x524281),$[_0x385ed4(0x106)]({'url':_0x385ed4(0x11e),'type':_0x385ed4(0x138),'data':_0x1905e9,'contentType':![],'processData':![],'success':function(_0x1f962c){const _0x18bf45=_0x385ed4,_0x1ef7ee=JSON['parse'](_0x1f962c);if(_0x1ef7ee['Success'])Alert_success(_0x18bf45(0x130)),document[_0x18bf45(0xb2)]('fanotactive')['style'][_0x18bf45(0xb3)]='none';else{if(_0x1ef7ee[_0x18bf45(0x108)])Alert_error(_0x1ef7ee[_0x18bf45(0x108)]);else _0x1ef7ee['Blocked']&&(window['location']['href']=_0x18bf45(0xd2));}},'error':function(_0x253a07){const _0x21b02d=_0x385ed4;Alert_error(_0x21b02d(0xe5));}});}function customInputBox(_0xa01fd3,_0x2f4649,_0x13e845,_0x16dcec,_0xecced7){return new Promise(_0x1334d7=>{const _0x1c2282=_0xf430,_0x89c655=document[_0x1c2282(0xee)]('div');_0x89c655['className']=_0x1c2282(0xb9);const _0x3a82ec=document[_0x1c2282(0xee)](_0x1c2282(0x105));_0x3a82ec[_0x1c2282(0xab)]=_0x1c2282(0x116),_0x3a82ec[_0x1c2282(0x10d)]='\x0a\x20\x20\x20\x20\x20\x20\x20\x20<p>'+_0x2f4649+_0x1c2282(0x121)+_0xa01fd3+_0x1c2282(0x122)+_0x16dcec+_0x1c2282(0xe0);const _0x15d8c0=_0x3a82ec[_0x1c2282(0xb4)]('.pin-input');_0x15d8c0[_0x1c2282(0xf8)]((_0x3a61c0,_0x37746f)=>{const _0x1cd972=_0x1c2282;_0x3a61c0[_0x1cd972(0xc3)](_0x1cd972(0xfd),_0x76a7d4=>{const _0x5833d1=_0x1cd972,_0x465345=_0x76a7d4[_0x5833d1(0xbc)][_0x5833d1(0xf7)];_0x465345[_0x5833d1(0xdf)]===0x1&&_0x37746f<_0x15d8c0['length']-0x1&&_0x15d8c0[_0x37746f+0x1][_0x5833d1(0xa8)]();if(Array[_0x5833d1(0xe4)](_0x15d8c0)[_0x5833d1(0xd6)](_0x1ca123=>_0x1ca123[_0x5833d1(0xf7)][_0x5833d1(0xdf)]===0x1)){const _0x3ec664=Array['from'](_0x15d8c0)[_0x5833d1(0x111)](_0x335e21=>_0x335e21[_0x5833d1(0xf7)])[_0x5833d1(0x115)]('');document[_0x5833d1(0x11a)][_0x5833d1(0xfa)](_0x89c655),document[_0x5833d1(0x11a)][_0x5833d1(0xfa)](_0x3a82ec),_0x1334d7(_0x3ec664);}}),_0x3a61c0['addEventListener'](_0x1cd972(0xd9),_0xdede25=>{const _0x853560=_0x1cd972;_0xdede25['key']===_0x853560(0xf5)&&_0x3a61c0[_0x853560(0xf7)]===''&&_0x37746f>0x0&&_0x15d8c0[_0x37746f-0x1][_0x853560(0xa8)]();});}),document[_0x1c2282(0x11a)]['appendChild'](_0x89c655),document[_0x1c2282(0x11a)][_0x1c2282(0x133)](_0x3a82ec),_0x15d8c0[0x0][_0x1c2282(0xa8)]();});}function setCookie(_0x38fa91,_0x4e46cf,_0x52f4e8){const _0x202e19=_0x5d4d70,_0x87767b=new Date();_0x87767b[_0x202e19(0xeb)](_0x87767b[_0x202e19(0xf0)]()+_0x52f4e8*0x18*0x3c*0x3c*0x3e8);const _0x2f7b11='expires='+_0x87767b[_0x202e19(0x10f)]();document[_0x202e19(0xe6)]=_0x38fa91+'='+_0x4e46cf+';'+_0x2f7b11+_0x202e19(0x134);}toastr['options']={'closeButton':!![],'debug':![],'newestOnTop':![],'progressBar':!![],'positionClass':'toast-bottom-right','preventDuplicates':![],'onclick':null,'showDuration':_0x5d4d70(0xc4),'hideDuration':_0x5d4d70(0xba),'timeOut':_0x5d4d70(0x136),'extendedTimeOut':_0x5d4d70(0xba),'showEasing':_0x5d4d70(0xa9),'hideEasing':_0x5d4d70(0xd3),'showMethod':'fadeIn','hideMethod':_0x5d4d70(0xc8)};function Alert_info(_0x2817f7,_0x44f781){const _0xbfc3d6=_0x5d4d70;toastr[_0xbfc3d6(0xc5)](_0x44f781,_0x2817f7);}function Alert_success(_0x1c5922,_0xdc7fe5){const _0x441756=_0x5d4d70;toastr[_0x441756(0xfb)](_0xdc7fe5,_0x1c5922);}function Alert_error(_0x2636f5,_0x431fb7){const _0x406104=_0x5d4d70;toastr[_0x406104(0x120)](_0x431fb7,_0x2636f5);}function getCookie(_0x26fc4b){const _0x4a815a=_0x5d4d70;var _0x550afa=document[_0x4a815a(0xe6)],_0x36106e=_0x26fc4b+'=',_0x36a743=_0x550afa[_0x4a815a(0x117)](_0x36106e);if(_0x36a743==-0x1)return null;_0x36a743+=_0x36106e[_0x4a815a(0xdf)];var _0x2c6a0d=_0x550afa[_0x4a815a(0x117)](';',_0x36a743);_0x2c6a0d==-0x1&&(_0x2c6a0d=_0x550afa[_0x4a815a(0xdf)]);var _0x4294f9=_0x550afa[_0x4a815a(0xb1)](_0x36a743,_0x2c6a0d);return decodeURIComponent(_0x4294f9);}let ccount=0x0;window[_0x5d4d70(0xc3)]('load',function(){const _0x56ac3f=_0x5d4d70,_0x33a01b=document[_0x56ac3f(0xb2)](_0x56ac3f(0x102)),_0x4e7ca7=document[_0x56ac3f(0xb2)]('dropdownflagList'),_0x521a71=document[_0x56ac3f(0xb2)](_0x56ac3f(0xf2));document[_0x56ac3f(0xc3)](_0x56ac3f(0xb6),function(){const _0x31f4c9=_0x56ac3f;!_0x33a01b['contains'](event[_0x31f4c9(0xbc)])&&!_0x4e7ca7['contains'](event[_0x31f4c9(0xbc)])&&_0x4e7ca7[_0x31f4c9(0xaf)][_0x31f4c9(0xed)](_0x31f4c9(0x11b));});const _0x4118eb={'EN':_0x56ac3f(0xc0),'CN':_0x56ac3f(0x137),'TR':_0x56ac3f(0x12b),'RU':_0x56ac3f(0xe2),'AR':_0x56ac3f(0xae)},_0x14fd2a={'EN':_0x56ac3f(0xd5)+_0x4118eb['CN']+_0x56ac3f(0x100)+_0x4118eb['AR']+'\x22\x20alt=\x22Arabic\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22TR\x22><img\x20src=\x22'+_0x4118eb['TR']+_0x56ac3f(0x124)+_0x4118eb['RU']+_0x56ac3f(0xfc),'CN':_0x56ac3f(0xad)+_0x4118eb['EN']+_0x56ac3f(0x125)+_0x4118eb['AR']+_0x56ac3f(0xcb)+_0x4118eb['TR']+_0x56ac3f(0x124)+_0x4118eb['RU']+'\x22\x20alt=\x22Russian\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20','AR':_0x56ac3f(0xad)+_0x4118eb['EN']+'\x22\x20alt=\x22USA\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22CN\x22><img\x20src=\x22'+_0x4118eb['CN']+_0x56ac3f(0xe3)+_0x4118eb['TR']+_0x56ac3f(0x124)+_0x4118eb['RU']+_0x56ac3f(0xfc),'TR':'\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22EN\x22><img\x20src=\x22'+_0x4118eb['EN']+'\x22\x20alt=\x22USA\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22CN\x22><img\x20src=\x22'+_0x4118eb['CN']+_0x56ac3f(0x100)+_0x4118eb['AR']+_0x56ac3f(0x13a)+_0x4118eb['RU']+_0x56ac3f(0xfc),'RU':_0x56ac3f(0xad)+_0x4118eb['EN']+_0x56ac3f(0x12a)+_0x4118eb['CN']+_0x56ac3f(0x100)+_0x4118eb['AR']+'\x22\x20alt=\x22Arabic\x20Flag\x22\x20class=\x22flag-icon\x22></li>\x0a\x20\x20\x20\x20\x20\x20\x20\x20<li\x20data-lang=\x22TR\x22><img\x20src=\x22'+_0x4118eb['TR']+_0x56ac3f(0xff)};_0x33a01b[_0x56ac3f(0xc3)](_0x56ac3f(0xb6),function(){const _0x3f6ad9=_0x56ac3f;_0x4e7ca7[_0x3f6ad9(0xaf)][_0x3f6ad9(0xef)](_0x3f6ad9(0x11b));}),_0x4e7ca7[_0x56ac3f(0xc3)]('click',function(_0x17529b){const _0x136377=_0x56ac3f;if(_0x17529b[_0x136377(0xbc)][_0x136377(0x11d)]===_0x136377(0x135)){const _0x46ba42=_0x17529b['target'][_0x136377(0xec)][_0x136377(0xca)](_0x136377(0x119));_0x1a0d0a(_0x46ba42);}});function _0x1a0d0a(_0x325e42){const _0x424b23=_0x56ac3f;_0x521a71[_0x424b23(0xc1)]=_0x4118eb[_0x325e42],_0x4e7ca7[_0x424b23(0x10d)]=_0x14fd2a[_0x325e42],localStorage[_0x424b23(0xc9)](_0x424b23(0x10a),_0x325e42),translatePage(_0x325e42);}function _0x55d2dd(){const _0x2508a1=_0x56ac3f,_0x5bda3a=localStorage['getItem'](_0x2508a1(0x10a));_0x5bda3a&&_0x1a0d0a(_0x5bda3a);}_0x55d2dd();var _0x51cfe6=document[_0x56ac3f(0xb2)]('csrf-token')[_0x56ac3f(0xd7)]('input');activationForm[_0x56ac3f(0x133)](_0x51cfe6),document[_0x56ac3f(0xb2)](_0x56ac3f(0x132))[_0x56ac3f(0x10d)]='';}),document[_0x5d4d70(0xb2)](_0x5d4d70(0xbb))['addEventListener']('keypress',function(_0x4055a7){const _0x3cf83d=_0x5d4d70;if(_0x4055a7['key']===_0x3cf83d(0x107)){let _0x41add3=new Event(_0x3cf83d(0xb6));loginButton[_0x3cf83d(0x139)](_0x41add3);}});function customMessageBox(_0x49cbb6,_0x21182d,_0x457311){return new Promise(_0x239ed2=>{const _0x13697a=_0xf430,_0x1b6492=document[_0x13697a(0xee)](_0x13697a(0x105));_0x1b6492['className']=_0x13697a(0xb9);const _0x4dae4a=document[_0x13697a(0xee)](_0x13697a(0x105));_0x4dae4a[_0x13697a(0xab)]=_0x13697a(0x116),_0x4dae4a['innerHTML']=_0x13697a(0x113)+_0x457311+_0x13697a(0xf4)+_0x21182d+_0x13697a(0xb5)+_0x49cbb6+_0x13697a(0xb0);const _0x5520dc=_0x4dae4a[_0x13697a(0xd7)](_0x13697a(0xea));_0x5520dc[_0x13697a(0xc3)](_0x13697a(0xb6),()=>{const _0x19c447=_0x13697a;document[_0x19c447(0x11a)][_0x19c447(0xfa)](_0x1b6492),document[_0x19c447(0x11a)]['removeChild'](_0x4dae4a),_0x239ed2(!![]);}),document[_0x13697a(0x11a)][_0x13697a(0x133)](_0x1b6492),document[_0x13697a(0x11a)][_0x13697a(0x133)](_0x4dae4a);});}
</script>




</html>