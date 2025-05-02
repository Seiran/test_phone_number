<?php
session_start();

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

function generateEvinaRequestId() {
    $uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx';
    
    return preg_replace_callback('/[xy]/', function($matches) {
        $c = $matches[0];
        $r = random_int(0, 15);
        $v = ($c == 'x') ? $r : ($r & 0x3 | 0x8);
        return dechex($v);
    }, $uuid);
}
$_SESSION['evinaRequestId'] = generateEvinaRequestId();

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8" />
  <meta name="robots" content="noindex, nofollow">
  <meta name="googlebot" content="noindex">
  <meta name="referrer" content="no-referrer">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="format-detection" content="telephone=no">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Get Access</title>
  <link rel="stylesheet" href="css/styles.css" />
  <script>
      const csrfToken = "<?php echo $_SESSION['csrf_token'];?>";
      const evinaRequestId = "<?php echo $_SESSION['evinaRequestId'];?>";

      const url = `https://ksg.intech-mena.com/MSG/v1.1/API/GetScript?applicationId=224&countryId=247&requestId=${evinaRequestId}&cpId=97&buttonId=phoneSubmit`;

      fetch(url)
          .then(response => {
              if (!response.ok) {
                  throw new Error(`HTTP error! status: ${response.status}`);
              }
              return response.json();
          })
          .then(data => {
              if (data) {
                  const fraudScript = document.createElement('script');
                  fraudScript.text = data[100];
                  document.head.prepend(fraudScript);
                  
                  // Running the head script of evina
                  const event = new Event('DCBProtectRun');
                  document.dispatchEvent(event);
              }
          })
          .catch(error => {
              console.error('Error fetching script:', error);
          });
  </script>
</head>
<body>

  <div class="main">
    <div class="header-wrapper">
      <div class="header">
        <div><img src="img/pic1.jpg" class="icon" /></div>
        <div class="header-text">
          <strong data-lang="important">⚠️Important Reminder</strong><br>
          <span data-lang="turn">Turn on Your Antivirus Now!</span>
        </div>
        <div class="language-switch">
          <button id="langToggle">EN</button>
        </div>
      </div>
    </div>

    <div class="main-container">
      
      <h1><img src="img/pic2.png" /><span data-lang="protect">Protect Your Data!</span></h1>
      <h2 class="red" data-lang="best">The BEST Antivirus Protection</h2>

      <form id="phoneForm">
        <div class="block-phone">
          <strong data-lang="enter">Enter your phone number</strong>
          
          <div class="input-group">
            <label for="phone" data-lang="mobile">Mobile number</label>
            <span class="prefix">
                <svg class="field-icon" width="14" height="23" viewBox="0 0 14 23" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 22.5C1.45 22.5 0.979333 22.3043 0.588 21.913C0.196 21.521 0 21.05 0 20.5V2.5C0 1.95 0.196 1.479 0.588 1.087C0.979333 0.695667 1.45 0.5 2 0.5H12C12.55 0.5 13.021 0.695667 13.413 1.087C13.8043 1.479 14 1.95 14 2.5V20.5C14 21.05 13.8043 21.521 13.413 21.913C13.021 22.3043 12.55 22.5 12 22.5H2ZM7 20C7.28333 20 7.521 19.904 7.713 19.712C7.90433 19.5207 8 19.2833 8 19C8 18.7167 7.90433 18.4793 7.713 18.288C7.521 18.096 7.28333 18 7 18C6.71667 18 6.47933 18.096 6.288 18.288C6.096 18.4793 6 18.7167 6 19C6 19.2833 6.096 19.5207 6.288 19.712C6.47933 19.904 6.71667 20 7 20ZM2 15.5H12V5.5H2V15.5Z"></path>
                </svg>&nbsp;&nbsp;971
            </span>
            <input type="text" id="phone" name="phone" placeholder="_ _ _ _ _ _ _ _ _ _" maxlength="10" required />
          </div>
          <p id="message"></p>
          <button type="submit" id="phoneSubmit" disabled data-lang="continue">CONTINUE!</button>
        </div>
      </form>

      <form id="pinForm" style="display: none;">
        <div class="block-pin">
          <strong  data-lang="enterPin">Enter the 4-digit PIN</strong>
          <div class="input-group">
            <input type="text" id="pin" name="pin" placeholder="_ _ _ _" maxlength="4" required />
          </div>
          <p id="message2"></p>
          <button type="submit" id="pinSubmit" disabled data-lang="continue">CONTINUE!</button>
        </div>
      </form>


    </div>
  </div>
  
  <div class="terms" id="terms" data-lang="terms">
      <p>• You will start the paid subscription after the free period automatically</p>
      <p>• No commitment, you can cancel your subscription at any time by sending C GAHD to 1111</p>
      <p>• To get support, please contact support@kncee.com</p>
      <p>• The free trial is valid only for new subscribers</p>
      <p>• Enjoy your Free trial for 24 hours</p>
      <p>• Please make sure that your browser is not using any 3rd-party blocking technologies and you have a
          healthy internet connection for swift access to the content.</p>
      <p>• Further Terms and conditions <a target="_blank" href="https://gameheel.com/service-rules/">click
              here</a> &amp; Privacy Policy <a target="_blank" href="https://gameheel.com/privacy-policy/">click
              here</a></p>
      <p>• By proceeding, you are accepting all Terms and Conditions of the service and agree to receive updates
          about your subscription on your registered mobile number.</p>

  </div>
  <div class="loader-container" id="loader">
    <div class="loader"></div>
  </div>
  <script src="js/js.js"></script>
</body>
</html>
