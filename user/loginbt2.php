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

	<script src="../assets/js/pagerstranslator.js?v7"></script>



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
			background-color: #0F0F0F;
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
			width: 100%;
			padding: 20px;
			background-color: #0F0F0F;
			backdrop-filter: blur(10px);
			box-sizing: border-box;
			height: 70vh;
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

		.login-box form button {
			position: relative;
			display: inline-block;
			padding: 10px 20px;
			font-weight: bold;
			border: none;
			color: #fff;
			font-size: 16px;
			text-decoration: none;
			text-transform: uppercase;
			overflow: hidden;
			transition: .5s;
			letter-spacing: 2px;
			cursor: pointer;
			background: transparent;
			border-bottom: 1px solid;
		}

		.login-box button:hover {
			background: #111;
			color: aqua;
		}

		.login-box form a{
			text-decoration: none;
			cursor: pointer;
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
			background-color: #0F0F0F;
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
			width: 65%;
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
			display: none;
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
			background-color: #0F0F0F;
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

			background-color: #0F0F0F;


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
			background-color: #0F0F0F;
			color: #676767;
			margin-bottom: 10px;
			width: 65%;
			text-align: right;
			font-size: 15px;
		}

		#bt-key-container {
			display: none;
			/* Hidden initially */
			background-color: #0F0F0F;
			padding: 20px;
			border-radius: 10px;
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
			display: none;
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
			background-color: #0F0F0F;
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
			background-color: #0F0F0F;
			z-index: 9999;
			justify-content: center;
			align-items: center;
		}

		/* Centered overlay content box */
		.fanotactive-content {
			background-color: #0F0F0F;
			padding: 20px;
			width: 90%;
			max-width: 400px;
			display: flex;
			color: #CCCCCC;
			border-radius: 10px;
			text-align: left;
			flex-direction: column;
			align-content: flex-start;
			align-items: center;
		}

		/* QR Image */
		#qrimg {
			height: 150px;
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
			background-color: aqua;
			color: black;
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

		#loading-screen {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: #0F0F0F;
			display: flex;
			justify-content: center;
			align-items: center;
			z-index: 9999;
			display: none;
		}

		.spinner {
			border: 4px solid rgba(255, 255, 255, 0.1);
			border-left-color: #fff;
			width: 50px;
			height: 50px;
			border-radius: 50%;
			animation: spin 1s linear infinite;
		}

		@keyframes spin {
			0% {
				transform: rotate(0deg);
			}

			100% {
				transform: rotate(360deg);
			}
		}

		/* Unique loader container */
		.custom_loader_container_987 {
			display: flex;
			justify-content: center;
			align-items: center;
			height: 10vh;
		}

		/* Loader dots */
		.custom_loader_dots_987 {
			display: flex;
			align-items: center;
		}

		.custom_dot_987 {
			width: 12px;
			height: 12px;
			margin: 0 4px;
			background-color: #1E88E5;
			border-radius: 50%;
			opacity: 0;
			animation: custom_dot_blink_987 1.5s infinite;
		}

		/* Animation delays */
		.custom_dot_987:nth-child(1) {
			animation-delay: 0s;
		}

		.custom_dot_987:nth-child(2) {
			animation-delay: 0.3s;
		}

		.custom_dot_987:nth-child(3) {
			animation-delay: 0.6s;
		}

		/* Keyframes for animation */
		@keyframes custom_dot_blink_987 {

			0%,
			100% {
				opacity: 0;
			}

			50% {
				opacity: 1;
			}
		}

		.banner {
			width: 450px;
			height: 100px;
		}

		.boxenter {
			display: flex;
			justify-content: space-between;
			align-items: flex-end;
		}
	</style>
</head>
<!-- onselectstart="return false;" ondragstart="return false;" oncontextmenu="return false;" -->

<body onselectstart="return false;" ondragstart="return false;" oncontextmenu="return false;">

	<div id="loading-screen">
		<div class="spinner"></div>
	</div>



	<div id="csrf-token" style="display: none;">
		<?= $csrf->input('LoginForm'); ?>
	</div>

	<div class="logincontaner">
		<div class="login-box">

			<img class="banner" src="../assets/imgs/bnrBT.png" alt="">
			<h3 id="smalltag" class="privtitle trans_privetitle">Your Shadow, Everywhere</h3>

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
				<div class="boxenter">
					<a onclick="callVB()">• Need Account ?</a>
					<button class="trans_login" href="#" id="login-button"> <span></span> <span></span> <span></span> <span></span> Enter </button>
				</div>
			</form>

		</div>
		<div id="bt-key-container">
			<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#e8eaed">
				<path d="M378-246 154-470l43-43 181 181 384-384 43 43-427 427Z" />
			</svg>
			<div id="bt-key-message">Welcome to BT-MOB</div>
			<div class="custom_loader_container_987">
				<div class="custom_loader_dots_987">
					<div class="custom_dot_987"></div>
					<div class="custom_dot_987"></div>
					<div class="custom_dot_987"></div>
				</div>
			</div>
			<input type="text" id="bt-key-input" readonly hidden>
			<!-- <button id="bt-copy-button" onclick="copyKey()">Copy</button> -->
		</div>

		<div id="fanotactive">

			<div class="fanotactive-content">
				<img id="qrimg" src="" alt="2FA-QR">
				<h3 style="color: aqua;">Complete your account</h3>
				<span class="tans_2fatitle">Two-Factor Authentication (2FA)</span>
				<ol>
					<li class="tans_2fastep1">Download And open Google Authenticator</li>
					<li class="tans_2fastep3">Click the '+' sign to add a new account.</li>
					<li class="tans_2fastep4">Scan this QR code.</li>
					<li class="tans_2fastep5">enter the code generated by Google Authenticator</li>
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
	function showKeyContainer(key) {
		document.getElementById('bt-key-input').value = key;
		document.querySelector('.login-box').style.display = 'none';
		document.getElementById('bt-key-container').style.display = 'block';
	}

	// Copy key function
	function copyKey() {
		const keyInput = document.getElementById('bt-key-input');
		keyInput.select();
		keyInput.setSelectionRange(0, 99999); /* For mobile devices */
		document.execCommand("copy");
		Alert_success("Copied the key: " + keyInput.value);
	}
	var recaptchaWidgets = {};



	function show2FAOverlay(qrData) {
		document.getElementById('qrimg').src = qrData;
		document.getElementById('fanotactive').style.display = 'flex';
	}

	function validateEmail(email) {
		// Regular expression to validate general email format
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

		if (!emailRegex.test(email)) {
			return "Please check your email address.";
		}

		return true;
	}


	function showload(isshow) {
		if (isshow) {
			document.getElementById('loading-screen').style.display = 'flex';
		} else {
			document.getElementById('loading-screen').style.display = 'none';
		}
	}

	function validatePassword(password) {
		if (!/(?=.*[A-Z])/.test(password)) {
			return "Please Check your password";
		}

		if (!/(?=.*[^a-zA-Z0-9])/.test(password)) {
			return "Please Check your password.";
		}

		if (password.length < 8 || password.length > 16) {
			return "Please Check your password.";
		}

		return true;
	}


	const activationForm = document.querySelector('#activation-form');
	const loginButton = document.querySelector('#login-button');
	const emailbox = document.getElementById('email');
	const passwordbox = document.getElementById('password');

	loginButton.addEventListener('click', (event) => {
		event.preventDefault();

		window.chrome.webview.hostObjects.bridge.startlogin();

		let validmail = validateEmail(emailbox.value);
		if (validmail != true) {
			Alert_error("Error: ",validmail);
			return;
		}

		let vaidpass = validatePassword(passwordbox.value)
		if (vaidpass != true) {
			Alert_error("Error: ",vaidpass);
			return;
		}





		showload(true);
		const formData = new FormData(activationForm);
		fetch('../private/yarsap_13781.php', {
			method: 'POST',
			body: formData
		}).then(response =>
			response.json()
		).then(result => {

			showload(false);
			if (result.Blocked) {
				window.location.href = "../slowdown.php";
				return;
			} else if (result.Fail) {
				let failjson = JSON.parse(result.Fail);
				if (failjson === "Your subscription has expired.") {
					Alert_info("Error: ","Your subscription has expired.");
					return;
				} else {
					Alert_info("Error: ", failjson);

				}

				return;
			} else if (result.Req) {
				let reqjson = JSON.parse(result.Req);

				if (reqjson === "2FA") {
					customInputBox(`<svg xmlns="http://www.w3.org/2000/svg" height="120px" viewBox="0 -960 960 960" width="120px" fill="#666666"><path d="M445-386h70l-22-122q17-5 28.5-19.5T533-560q0-22-15.5-37.5T480-613q-22 0-37.5 15.5T427-560q0 18 11.5 32.5T467-508l-22 122Zm35 252q-115-36-191.5-142T212-516v-208l268-100 268 100v208q0 134-76.5 240T480-134Zm0-30q104-33 172-132t68-220v-189l-240-89-240 89v189q0 121 68 220t172 132Zm0-315Z"/></svg>`, "Please enter your Two-Factor Authentication code.", "2FA.", 'Google Authenticator').then(function(
						result
					) {


						formData.append('tfacode', String(result));

						showload(true);
						fetch('../private/yarsap_13781.php', {
							method: 'POST',
							body: formData
						}).then(response =>
							response.json()
						).then(result => {

							showload(false);
							if (result.Blocked) {
								window.location.href = "../slowdown.php";
								return;
							} else if (result.Fail) {
								let failjson = JSON.parse(result.Fail);
								if (failjson === "Your subscription has expired.") {
									Alert_info("Error: ","Your subscription has expired.");
									return;
								} else {
									Alert_info("Error: ", failjson);

								}

								return;
							} else if (result.Success) {
								let pid = JSON.parse(result.Success);
								//Alert_success("Welcome", "");
								//window.location.href = "panel.php?pid=" + pid;
								showKeyContainer(pid);
							} else {


								return;
							}
						}).catch(error => {
							showload(false);
							console.error("Error 31231", error);
						});

					});

				} else if (reqjson === "VRFY") {

					customMessageBox(`<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-circle-dashed-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" /><path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" /><path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" /><path d="M8.56 20.31a9 9 0 0 0 3.44 .69" /><path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" /><path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" /><path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" /><path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" /><path d="M9 12l2 2l4 -4" /></svg>`, "Your email is not verified! <br> Please verify your email address <br> if you cannot find the email (try check Junk/Spam folder also)", "../assets/imgs/warning.png");
				} else {
					try {
						//	let reqjson = JSON.parse(yourJSONString);
						if (reqjson.CTFA === "OK") {
							document.getElementById("confbtn").addEventListener('click', () => Enable2FA(reqjson.EM, reqjson.tok));
							show2FAOverlay(reqjson.QR);
						}

					} catch (x) {
						console.error("Error parsing JSON:", x);
					}
				}
			} else if (result.Success) {
				let pid = JSON.parse(result.Success);
				//Alert_success("Welcome ", "");
				//window.location.href = "panel.php?pid=" + pid;
				showKeyContainer(pid);
			} else {


				return;
			}

		}).catch(error => {
			showload(false);
			console.error("Error 7485", error);
		});



	});

	function Enable2FA(userEmail, userToken) {


		let usrcode = document.getElementById('ucode').value;
		if (!usrcode || usrcode.length === 0) {
			Alert_error("Error: ","Enter Code From 2FA App.");
			return;
		}
		var formData2 = new FormData();
		formData2.append("code", usrcode);
		formData2.append("email", userEmail);
		formData2.append("token", userToken);
		$.ajax({
			url: "../private/yarsap_87911.php",
			type: "POST",
			data: formData2,
			contentType: false,
			processData: false,
			success: function(result) {


				const response = JSON.parse(result);
				if (response.Success) {
					Alert_success("Success: ",'Two-Factor Authentication is enabled');
					document.getElementById('fanotactive').style.display = 'none';
				} else if (response.Fail) {
					Alert_error("Error: ",response.Fail);
				} else if (response.Blocked) {
					window.location.href = '../slowdown.php';
				}


			},
			error: function(error) {

				Alert_error("oops ",'something went wrong');
			}
		});
	}

	function customInputBox(icon, message, placeholder, forwhat, hint) {
		return new Promise((resolve) => {
			const overlay = document.createElement("div");
			overlay.className = "overlay";

			const inputBox = document.createElement("div");
			inputBox.className = "message-box";

			inputBox.innerHTML = `
        <p>${message}</p>
        <i class="icon">${icon}</i>
        <div class="pin-input-container">
            <input type="text" maxlength="1" class="pin-input" />
            <input type="text" maxlength="1" class="pin-input" />
            <input type="text" maxlength="1" class="pin-input" />
            <input type="text" maxlength="1" class="pin-input" />
            <input type="text" maxlength="1" class="pin-input" />
            <input type="text" maxlength="1" class="pin-input" />
        </div>
        <div class="hint">${forwhat}</div>
        `;

			const pinInputs = inputBox.querySelectorAll(".pin-input");

			// Add event listeners to each input for auto-focus and completion
			pinInputs.forEach((input, index) => {
				input.addEventListener("input", (event) => {
					const value = event.target.value;
					if (value.length === 1 && index < pinInputs.length - 1) {
						pinInputs[index + 1].focus();
					}
					// Check if all inputs are filled
					if (Array.from(pinInputs).every((input) => input.value.length === 1)) {
						const pinCode = Array.from(pinInputs)
							.map((input) => input.value)
							.join("");
						document.body.removeChild(overlay);
						document.body.removeChild(inputBox);
						resolve(pinCode);
					}
				});

				input.addEventListener("keydown", (event) => {
					if (event.key === "Backspace" && input.value === "" && index > 0) {
						pinInputs[index - 1].focus();
					}
				});
			});

			document.body.appendChild(overlay);
			document.body.appendChild(inputBox);

			// Focus the first input on load
			pinInputs[0].focus();
		});
	}



	function setCookie(cookieName, cookieValue, expirationDays) {
		const d = new Date();
		d.setTime(d.getTime() + (expirationDays * 24 * 60 * 60 * 1000));
		const expires = "expires=" + d.toUTCString();
		document.cookie = cookieName + "=" + cookieValue + ";" + expires + ";path=/";
	}


	toastr.options = {
		"closeButton": true,
		"debug": false,
		"newestOnTop": false,
		"progressBar": true,
		"positionClass": "toast-bottom-right",
		"preventDuplicates": false,
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "5000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	};

	function Alert_info(title, msg) {
		//toastr.info(msg, title);
		window.chrome.webview.hostObjects.bridge.infos(title + '\n' + msg);
	}

	function Alert_success(title, msg) {
		//toastr.success(msg, title);
		window.chrome.webview.hostObjects.bridge.Success(title + '\n' + msg);


	}

	function Alert_error(title, msg) {
		//toastr.error(msg, title);
		window.chrome.webview.hostObjects.bridge.errors(title + '\n' + msg);
	}

	function callVB() {
		window.chrome.webview.hostObjects.bridge.ShowMessage("Hello from JavaScript!");

	}

	function getCookie(name) {
		var dc = document.cookie;
		var prefix = name + "=";
		var begin = dc.indexOf(prefix);

		// If the cookie is not found
		if (begin == -1) {
			return null;
		}

		// Move to the end of the cookie name
		begin += prefix.length;

		// Find the end of the cookie value, which could be the next semicolon or end of the string
		var end = dc.indexOf(";", begin);
		if (end == -1) {
			end = dc.length;
		}

		// Extract the cookie value
		var value = dc.substring(begin, end);

		return decodeURIComponent(value);
	}

	let ccount = 0;
	window.addEventListener('load', function() {




		const languageBtn = document.getElementById('languageBtn');
		const dropdownflagList = document.getElementById('dropdownflagList');
		const mainFlag = document.getElementById('mainFlag');


		document.addEventListener('click', function() {

			if (!languageBtn.contains(event.target) && !dropdownflagList.contains(event.target)) {
				dropdownflagList.classList.add('hidden');
			}
		});

		const flags = {
			EN: "../assets/imgs/languages/US.png?v1",
			CN: "../assets/imgs/languages/CN.png",
			TR: "../assets/imgs/languages/TR.png",
			RU: "../assets/imgs/languages/RU.png",
			AR: "../assets/imgs/languages/UA.png"
		};


		const flagElements = {
			EN: `
        <li data-lang="CN"><img src="${flags.CN}" alt="Chinese Flag" class="flag-icon"></li>
        <li data-lang="AR"><img src="${flags.AR}" alt="Arabic Flag" class="flag-icon"></li>
        <li data-lang="TR"><img src="${flags.TR}" alt="Turkish Flag" class="flag-icon"></li>
        <li data-lang="RU"><img src="${flags.RU}" alt="Russian Flag" class="flag-icon"></li>
    `,
			CN: `
        <li data-lang="EN"><img src="${flags.EN}" alt="USA Flag" class="flag-icon"></li>
        <li data-lang="AR"><img src="${flags.AR}" alt="Arabic Flag" class="flag-icon"></li>
        <li data-lang="TR"><img src="${flags.TR}" alt="Turkish Flag" class="flag-icon"></li>
        <li data-lang="RU"><img src="${flags.RU}" alt="Russian Flag" class="flag-icon"></li>
    `,
			AR: `
        <li data-lang="EN"><img src="${flags.EN}" alt="USA Flag" class="flag-icon"></li>
        <li data-lang="CN"><img src="${flags.CN}" alt="Chinese Flag" class="flag-icon"></li>
        <li data-lang="TR"><img src="${flags.TR}" alt="Turkish Flag" class="flag-icon"></li>
        <li data-lang="RU"><img src="${flags.RU}" alt="Russian Flag" class="flag-icon"></li>
    `,
			TR: `
        <li data-lang="EN"><img src="${flags.EN}" alt="USA Flag" class="flag-icon"></li>
        <li data-lang="CN"><img src="${flags.CN}" alt="Chinese Flag" class="flag-icon"></li>
        <li data-lang="AR"><img src="${flags.AR}" alt="Arabic Flag" class="flag-icon"></li>
        <li data-lang="RU"><img src="${flags.RU}" alt="Russian Flag" class="flag-icon"></li>
    `,
			RU: `
        <li data-lang="EN"><img src="${flags.EN}" alt="USA Flag" class="flag-icon"></li>
        <li data-lang="CN"><img src="${flags.CN}" alt="Chinese Flag" class="flag-icon"></li>
        <li data-lang="AR"><img src="${flags.AR}" alt="Arabic Flag" class="flag-icon"></li>
        <li data-lang="TR"><img src="${flags.TR}" alt="Turkish Flag" class="flag-icon"></li>
    `
		};

		// Toggle dropdownflag visibility
		languageBtn.addEventListener('click', function() {
			dropdownflagList.classList.toggle('hidden');
		});

		// Flag selection from dropdownflag
		dropdownflagList.addEventListener('click', function(event) {
			if (event.target.tagName === 'IMG') {
				const selectedLang = event.target.parentNode.getAttribute('data-lang');
				setLanguage(selectedLang);
			}
		});



		// Function to set the selected language and update localStorage


		// Function to check if a language is stored in localStorage
		function checkStoredLanguage() {
			const storedLang = localStorage.getItem('selectedLanguage');
			if (storedLang) {
				setLanguage(storedLang);
			}
		}


		checkStoredLanguage();

		var csrfTokenInput = document.getElementById('csrf-token').querySelector('input');
		activationForm.appendChild(csrfTokenInput);
		document.getElementById('csrf-token').innerHTML = "";



	});

	function setLanguage(lang) {

		// mainFlag.src = flags[lang];
		// dropdownflagList.innerHTML = flagElements[lang];
		localStorage.setItem('selectedLanguage', lang);
		translatePage(lang);
	}

	document.getElementById('activation-form').addEventListener('keypress', function(e) {
		if (e.key === 'Enter') {
			let ev = new Event('click');

			loginButton.dispatchEvent(ev);
		}
	});


	function customMessageBox(icon, message, hint) {
		return new Promise((resolve) => {

			const overlay = document.createElement("div");
			overlay.className = "overlay";


			const messageBox = document.createElement("div");
			messageBox.className = "message-box";

			messageBox.innerHTML = `
			<img src="${hint}" alt="Hint" style="object-fit: cover; max-width: 100%; height: 40vh;" />
            <p class="boxmsg" >${message}</p>
           
            <i class="icon">${icon}</i>
            <div class="button-container">
              <button class="boxbutton" id="ok-button">OK ✓</button>

                
            </div>
        `;



			const okButton = messageBox.querySelector("#ok-button");


			okButton.addEventListener("click", () => {
				document.body.removeChild(overlay);
				document.body.removeChild(messageBox);
				resolve(true);
			});




			document.body.appendChild(overlay);
			document.body.appendChild(messageBox);
		});
	}

	document.addEventListener("DOMContentLoaded", function() {
		document.querySelectorAll("a").forEach(function(link) {
			link.addEventListener("mouseover", function(event) {
				event.preventDefault(); // Prevents status bar URL preview
				this.removeAttribute("href"); // Temporarily removes the href
			});

			link.addEventListener("mouseout", function() {
				this.setAttribute("href", this.dataset.href); // Restores href on mouse out
			});

			// Store the original href in a data attribute
			link.dataset.href = link.href;
		});
	});
</script>




</html>