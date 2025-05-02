document.addEventListener("DOMContentLoaded", () => {
  const phoneInput = document.getElementById("phone");
  const pinInput = document.getElementById("pin");
  const phoneForm = document.getElementById("phoneForm");
  const pinForm = document.getElementById("pinForm");
  const message = document.getElementById("message");
  const message2 = document.getElementById("message2");
  const langToggle = document.getElementById("langToggle");
  const phoneSubmit = document.getElementById("phoneSubmit");

  const texts = {
    en: {
      protect: "Protect Your Data!",
      best: "The BEST Antivirus Protection",
      enter: "Enter your phone number",
      enterPin: "Enter the 4-digit PIN",
      continue: "CONTINUE!",
      confirmed: "Number confirmed!",
      important: "️⚠️Important Reminder",
      turn: "Turn on Your Antivirus Now!",
      mobile: "Mobile number",
      invalid: "Invalid phone number",
      incorrect: "Incorrect PIN",
      terms: ' ' +
          '<p>• You will start the paid subscription after the free period automatically</p>' +
          '<p>• No commitment, you can cancel your subscription at any time by sending C GAHD to 1111</p>' +
          '<p>• To get support, please contact support@kncee.com</p>' +
          '<p>• The free trial is valid only for new subscribers</p>' +
          '<p>• Enjoy your Free trial for 24 hours</p>' +
          '<p>• Please make sure that your browser is not using any 3rd-party blocking technologies and you have a healthy internet connection for swift access to the content.</p>' +
          '<p>• Further Terms and conditions <a target="_blank" href="https://gameheel.com/service-rules/">click here</a> &amp; Privacy Policy <a target="_blank" href="https://gameheel.com/privacy-policy/">click here</a></p>' +
          '<p>• By proceeding, you are accepting all Terms and Conditions of the service and agree to receive updates about your subscription on your registered mobile number.</p>',
            
    },
    ar: {
      protect: "احمِ بياناتك!",
      best: "أفضل حماية من الفيروسات",
      enter: "أدخل رقم هاتفك",
      enterPin: "أدخل رمز التحقق (4 أرقام)",
      continue: "استمر!",
      confirmed: "تم تأكيد الرقم!",
      important: "تذكير هام⚠️",
      turn: "قم بتشغيل برنامج مكافحة الفيروسات الخاص بك الآن!",
      mobile: "رقم الهاتف المحمول",
      invalid: "رقم الهاتف غير صالح",
      incorrect: "رقم التعريف الشخصي غير صحيح",
      terms: ' ' +
          '<p>• سيتم بدء الاشتراك المدفوع بعد الفترة المجانية تلقائيًا</p>' +
          '<p>• لا يوجد التزام، يمكنك إلغاء اشتراكك في أي وقت عن طريق إرسال C GAHD إلى 1111</p>' +
          '<p>• للحصول على الدعم، يرجى الاتصال بـ support@kncee.com</p>' +
          '<p>• النسخة التجريبية المجانية صالحة فقط للمشتركين الجدد</p>' +
          '<p>• استمتع بالتجربة المجانية لمدة 24 ساعة</p>' +
          '<p>• يرجى التأكد من أن متصفحك لا يستخدم أي تقنيات حظر تابعة لجهات خارجية وأن لديك اتصالاً بالإنترنت سليمًا للوصول السريع إلى المحتوى.</p>' +
          '<p>• للمزيد من الشروط والأحكام <a target="_blank" href="https://gameheel.com/service-rules/">انقر هنا</a> &amp; سياسة الخصوصية <a target="_blank" href="https://gameheel.com/privacy-policy/">انقر هنا</a></p>' +
          '<p>• من خلال المتابعة، فإنك تقبل جميع الشروط والأحكام الخاصة بالخدمة وتوافق على تلقي التحديثات حول اشتراكك على رقم الهاتف المحمول المسجل لديك.</p>',
    }
  };

  let currentLang = "en";

  const updateLanguage = () => {
    document.querySelectorAll("[data-lang]").forEach(el => {
      el.innerHTML = texts[currentLang][el.dataset.lang];
    });
    langToggle.textContent = currentLang.toUpperCase();
    document.body.dir = currentLang === "ar" ? "rtl" : "ltr";
  };

  langToggle.addEventListener("click", () => {
    currentLang = currentLang === "en" ? "ar" : "en";
    updateLanguage();
  });

  phoneInput.addEventListener("input", () => {
    //phoneSubmit.disabled = !/^\d{5,15}$/.test(phoneInput.value);
    phoneNumber = phoneInput.value;
    if (phoneNumber.match(/^0[0-9]{9}$/gm) == null && phoneNumber.match(/^[0-9]{9}$/gm) == null) {
        phoneSubmit.disabled = true;
    } else {
        phoneSubmit.disabled = false;
    }
    message.textContent = "";
  });
  
  pinInput.addEventListener("input", () => {
    pinSubmit.disabled = !/^\d{4}$/.test(pinInput.value);
    message2.textContent = "";
  });

  phoneForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    message.textContent = "";
    showLoader();

    try {
      const response = await fetch("tel.php", {
        method: "POST",
        body: new URLSearchParams({ phone: phoneInput.value, csrf_token: csrfToken })
      });

      const result = await response.json();

      if (result.error) {
        message.textContent = result.error in texts[currentLang] 
          ? texts[currentLang][result.error] 
          : result.error;
      } else {
        phoneForm.style.display = "none";
        pinForm.style.display = "block";
      }
    } catch (error) {
      message.textContent = "Error";
    } finally {
      hideLoader();
    }
  });

  pinForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    message2.textContent = "";
    showLoader();

    try {
      if (!/^\d{4}$/.test(pinInput.value)) {
        message2.textContent = "PIN must be 4 digits.";
        return;
      }

      const response = await fetch("tel.php", {
        method: "POST",
        body: new URLSearchParams({ pin: pinInput.value, csrf_token: csrfToken })
      });

      const result = await response.json();

      if (result.error) {
        message2.textContent = result.error in texts[currentLang] ? texts[currentLang][result.error] : result.error;
      } else {
        message2.style.color = "green";
        message2.textContent = texts[currentLang].confirmed;
      }
    } catch (error) {
      message2.textContent = "Error";
    } finally {
      hideLoader();
    }
  });

  updateLanguage();
  
  function showLoader() {
    document.getElementById('loader').style.display = 'flex';
  }

  function hideLoader() {
    document.getElementById('loader').style.display = 'none';
  }  
  
});
