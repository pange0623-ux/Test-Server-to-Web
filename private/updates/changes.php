<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Notes - V2</title>
    <style>
        *::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-corner {
            background: rgba(0, 0, 0, 0);
            border-radius: 5px;
        }


        ::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 15px;
            margin-top: 30px;
            margin-bottom: 30px;
            width: 5px;
        }

        *::-webkit-scrollbar-thumb {
            background: var(--sb-thumb-color);
            border-radius: 15px;
            width: 5px;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #0F0F0F;
            color: #ffffff;
            padding: 20px;
            -webkit-user-select: none;
            /* For Chrome, Safari */
            -moz-user-select: none;
            /* For Firefox */
            -ms-user-select: none;
            /* For IE/Edge */
            user-select: none;
            /* Standard */
            pointer-events: none;
        }

        .changelog {

            padding: 20px;
            border-radius: 10px;
        }

        h1 {
            margin-left: 10px;
        }

        .update {
            margin-bottom: 20px;
            padding: 10px;
            border-left: 4px solid #1E88E5;
            background-color: #2a2a2a;
            border-radius: 5px;
        }

        .version {
            font-weight: bold;
            color: #1E88E5;
        }

        .important {
            font-weight: bold;
            color: #F00;
        }

        li {
            margin: 0 0 10px 0;
        }
        .titlebrg{
            color: #1E88E5;
            font-weight:bold;
            font-size: 1.7rem;
        }
    </style>
</head>

<body onselectstart="return false;" ondragstart="return false;" oncontextmenu="return false;">
    <div class="changelog">
        <h1>BTMOB Updates</h1>
		 <div class="update">
            <p class="version">Version V3.4.1 | 26 May 2025</p>
            <ul>
                <li class="titlebrg">General</li>
                <ul>
                    <li>Improvements and Bug fixes for BTMOB + APK</li>

                </ul>
            </ul>
        </div>
        <div class="update">
            <p class="version">Version V3.4 | 22 May 2025</p>
            <ul>
                <li class="titlebrg">Live Screen</li>
                <ul>
                    <li>Long press support added.</li>
                    <li>Recording feature added.</li>
                    <li>Improved speed and performance.</li>
                </ul>
            </ul>
            <ul>
                <li class="titlebrg">APK Improvements</li>
                <ul>
                    <li>Reduced APK size to 8MB (with dropper).</li>
                    <li>New APK encryption + Anti decompile.</li>
                    <li>Improved anti-delete functionality.</li>
                    <li>Fixed dropper stuck on "Installing"</li>
                    <li>Bug fixes and performance enhancements.</li>
                    <li>More accurate ping speed testing to server (ms).</li>
                    <li>Better background operation and connection stability.</li>
                </ul>
            </ul>
            <ul>
                <li class="titlebrg">Black Screen (New Block Modes)</li>
                <ul>
                    <li>Normal (Black)</li>
                    <li>System Update</li>
                    <li>Device Locked</li>
                    <li>Battery Died</li>
                </ul>
            </ul>
            <ul>
                <li class="titlebrg">Injection Lab</li>
                <ul>
                    <li>View all injections (Preview + Editor).</li>
                    <li>Edit, save, add, or remove injections.</li>
                </ul>
            </ul>
            <ul>
                <li class="titlebrg">Saved Login Info</li>
                <ul>
                    <li>2FA is not required on every login, only in certain cases.</li>
                    <li>To switch accounts, go to Settings > Logout.</li>
                </ul>
            </ul>
            <ul>
                <li class="titlebrg">General</li>
                <ul>
                                    <li>Full and automatic Injection System.</li>
                <li>Shows real-time connection speed to the server.</li>
                <li>Hide Permission Screen Redesigned.</li>
                <li>Fixed live notifications weren't showing in BTMOB.</li>
                <li>Redirect clients to a new server or email account.</li>
                <li>SMS Sending Function: send SMS using all detected SIM cards.</li>
                <li>Offline Keylogs : Auto Save,,Download All,Delete All</li>
                <li>Bug fixes and performance enhancements.</li>
                </ul>


            </ul>
        </div>
        <div class="update">
            <p class="version">Version V3.3 | 24 April 2025</p>
            <ul>
                <li>Improved anti-delete and anti-reset accessibility</li>
                <li>Enhanced APK stability and connection</li>
                <li>Added Spanish and Portuguese for the Request Accessibility page</li>
                <li>New Request Accessibility page for Samsung devices</li>
                <li>Update Accessibility page design</li>
                <li>Return the "Fake Uninstall" option to hide the icon</li>
                <li>New APK signature</li>
                <li>Updated APK and dropper encryption</li>
            </ul>
        </div>
        <div class="update">
            <p class="version">APK UPDATE V3.2.1 | 15 April 2025</p>
            <ul>
                <li>General Apk optimization</li>
            </ul>
        </div>
        <div class="update">
            <p class="version">Version 3.2 , 13 April 2025 </p>
            <ul>
                <li>Added live location monitoring</li>
                <li>Optimized and improved the APK</li>
                <li>APK Fixed bugs</li>
                <li>APK new encryption</li>
                <li>start live screen even when the screen is locked</li>
                <li>Live screen will auto-reconnect if the connection is lost</li>
                <li>Added "Connection Key" on RAT login</li>
                <li>Updated the Dropper</li>
                <li>Dropper new encryption</li>
                <li>Dropper don't hides its icon; it launches the main APK</li>
                <li>General bug fixes and improvements</li>
                <li>Tip: Use Dropper with "Hide Icon" option</li>
                </br>
                <li>Disabled BTMOB device hardware lock – you can now change your PC anytime</li>
                </br>
                <p class="important">use BTMOB v3.2 to build apk, old versions does not support Connection Key.</p>

            </ul>
        </div>
        <div class="update">
            <p class="version">Version 3 Fix , 30 March 2025</p>
            <ul>
                <li>Fix Accessibility crash after mobile restart</li>
                <li>Fix issue live screen not always works</li>
                <li>Performance and bug fixes</li>
            </ul>
        </div>
        <div class="update">
            <p class="version">Version 3.0 , 27 March 2025</p>
            <ul>
                <li>Automatic RAT updates</li>
                <li>Full auto-permission support for Android 14 & 15</li>
                <li>Major performance improvements</li>
                <li>Updated RAT login UI</li>
                <li>Suppress accessibility usage warning</li>
                <li>Interface improvements</li>
                <li>Apktool updated to v2.11.1</li>
                <li>New APK encryption</li>
                <li>General fixes and improvements</li>
                <li>Capture Alipay PIN</li>
                <li>App hide icon if user try to delete the app</li>
                <li>Automatically hide notification</li>
                <li>Improve connection stability</li>
                <li>Reworked “Guide” accessibility install method</li>
            </ul>
        </div>
        <div class="update">
            <p class="version">Version 2.9</p>
            <ul>
                <li>Videos: APK install with/without black screen</li>
                <li>Android 15 support</li>
                <li>Optional Notification Permission</li>
                <li>Anti-Factory Reset</li>
                <li>Auto-disable Google Play</li>
                <li>Hide permissions with black screen</li>
                <li>Performance and bug fixes</li>
            </ul>
        </div>

        <div class="update">
            <p class="version">Version 2.7</p>
            <ul>
                <li>Videos demonstrating auto permissions and Anti-Delete</li>
                <li>BT Panel: Edit/Rename client, Notes, Redirect, save APK builder config</li>
                <li>APK: Light & Angle sensor monitors</li>
                <li>Fixes: Camera/mic block detection, screen wake-up, permissions, Anti-Delete</li>
            </ul>
        </div>

        <div class="update">
            <p class="version">Version 2.5.5</p>
            <ul>
                <li>Dropper encryption update</li>
                <li>New loading screen</li>
                <li>Permission request removed from Dropper</li>
                <li>Dropper auto-installs & hides icon</li>
                <li>Fixes for high client volume issues and black screen bugs</li>
            </ul>
        </div>


        <div class="update">
            <p class="version">Version 2.5.2</p>
            <ul>
                <li>Server availability fixes</li>
                <li>Direct connection to server (no proxy/domain)</li>
                <li>Multiple global servers released</li>
                <li>General improvements</li>
            </ul>
        </div>

        <div class="update">
            <p class="version">Version 2.5</p>
            <ul>
                <li>SDK updated to Android 14</li>
                <li>Removed sticky notification</li>
                <li>Main website loads after permission grant</li>
                <li>Lock screen capture fully fixed</li>
                <li>Auto-grant file and draw-over permissions</li>
                <li>Live Screen: Silent mode black screen</li>
                <li>Added HTML APK injection + status icon</li>
                <li>Screen Reader with font size/color support</li>
                <li>Login window: Custom server input</li>
            </ul>
        </div>

        <div class="update">
            <p class="version">BTMOB V2 Release</p>
            <ul>
                <li><strong>Core Updates:</strong></li>
                <li>Redesigned with multi-threaded connections for better speed</li>
                <li>Dedicated connections for high-speed tools</li>
                <li>Dark UI theme and improved interface</li>
                <li>Auto-save for data (apps, SMS, contacts)</li>

                <li><strong>File Manager Updates:</strong></li>
                <li>FastObjListView for better performance</li>
                <li>Better search, preview support, built-in video player</li>
                <li>Enhanced download control</li>

                <li><strong>Lock Screen Tool:</strong> 4/6 digit PIN, Password support</li>

                <li><strong>Apps Manager:</strong> New columns, options to lock/track apps</li>

                <li><strong>General Enhancements:</strong></li>
                <li>APK improvements, lag fixes, connection stability</li>
                <li>Prevent Sleep Tool update</li>
            </ul>
        </div>

        <div class="update">
            <p class="version">BTMOB Update 1.7</p>
            <ul>
                <li>Video 1: Testing Android 13 Xiaomi</li>
                <li>Video 2: Testing Android 11 Samsung</li>
                <li>New Tool: Lock Screen (PIN + Pattern support, customizable)</li>
                <li>Bug fixes and improvements</li>
            </ul>
        </div>

        <div class="update">
            <p class="version">BTMOB Initial Features</p>
            <ul>
                <li>No port needed</li>
                <li>No VPN needed</li>
                <li>Powerful & Easy to use</li>
                <li>Clients page</li>
                <li>Screens Page: View multiple screen at same time</li>
                <li>Alerts page: view notification/alerts from clients</li>
                <li>Connections page</li>
                <li>Block list page</li>
                <li>Broadcast Manager: Send alerts/notifications with actions</li>
                <li>All tools contain search bar (files, apps, SMS, etc.)</li>
                <li>1 Manager: Full phone control, settings, permissions, offline data</li>
                <li>2 File Manager: Previews, scanning, full file control</li>
                <li>3 Live Screen: Multiple modes, control, quality/audio options</li>
                <li>4 Live Camera</li>
                <li>5 Live Microphone + record</li>
                <li>6 Read and Send Messages</li>
                <li>7 Read/Add/Delete contacts</li>
                <li>8 Apps Manager: Manage and track apps</li>
                <li>9 Live keylogger</li>
                <li>10 Web Browser: Hidden browser, login capture</li>
                <li>11 Tracking Manager</li>
                <li>12 Live chat</li>
                <li>13 Manage clients: Block/restart/stop app/uninstall</li>
            </ul>
        </div>


    </div>


</body>

</html>