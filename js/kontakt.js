$(document).ready( function() {
    
    $("#contact_form").append(
        "<p>"
        + "Damit wir uns mit Ihnen in Verbindung setzen können, geben Sie bitte mindestens Ihre Telefonnummer oder Ihre E-Mail-Adresse an. "
        + "Zum Schutz Ihrer persönlichen Daten ist diese Seite SSL geschützt."
        + "</p>"
        + "<p></p>"
    );
    
    $("#contact_form").append(
        "<form action=\"\" id=\"contact\">"
        + "<table>"
        + "<tr><td><label for=\"contact_company\">Firma</label>:</td><td><input type=\"text\" id=\"contact_company\" class=\"text\"></td></tr>"
        + "<tr><td><label for=\"contact_name\">Ansprechpartner</label>:</td><td><input type=\"text\" id=\"contact_name\" class=\"text\"></td></tr>"
        + "<tr><td><label for=\"contact_phone\">Telefon</label>:</td><td><input type=\"text\" id=\"contact_phone\" class=\"text\"></td></tr>"
        + "<tr><td><label for=\"contact_mail\">E-Mail</label>:</td><td><input type=\"text\" id=\"contact_mail\" class=\"text\"></td></tr>"
        + "<tr><td><label for=\"contact_msg\">Nachricht</label>:</td><td><textarea id=\"contact_msg\" rows=\"10\"></textarea></td></tr>"
        + "<tr><td>&nbsp;</td><td class=\"button\"><input type=\"submit\" value=\"Absenden\" class=\"button\">"
        + '<img src="images/button_absenden.gif" alt=\"Absenden\" id=\"contact_submit\"></td></tr>'
        + "</table>"
        + "</form>"
    );
    
    $("#contact_form input[type='text'], #contact_form textarea").bind( "focus", function() {
        $(this).addClass("focus"); 
    });
    
    $("#contact_form input[type='text'], #contact_form textarea").bind( "blur", function() {
        $(this).removeClass("focus"); 
    });
    
    $("#contact_company").get(0).focus();
    
    $("#contact_submit").bind("click", function() {
        $("#contact").trigger("submit");
    });
    
    $("#contact").bind( "submit", function() {
       
       var company = $("#contact_company");
       var name    = $("#contact_name");
       var phone   = $("#contact_phone");
       var mail    = $("#contact_mail");
       var msg     = $("#contact_msg");
       
       var data = new Array();
       
       var required = new Array(company, name, msg);
       for(var i = 0; i < required.length; i++) {
        
            if(isEmpty(required[i].val())) {
                required[i].get(0).focus();
                alert("Bitte füllen Sie das Feld \"" + $("#contact_form label[for='" + required[i].attr("id") + "']").text() + "\" aus!");
                return false;
            }
            else {
                data[required[i].attr("id")] = required[i].val().replace(/<[^<>]*>/g, "");  // wir wollen keine HTML-Tags in unserer Nachricht
            }
       }
       
       if(isEmpty(phone.val()) && isEmpty(mail.val())) {
            phone.get(0).focus();
            alert("Bitte geben Sie entweder Ihre Telefonnummer oder Ihre E-Mail-Adresse ein, damit wir Sie kontaktieren können!");
            return false;
       }
       
       var mailPattern = /^[a-zA-Z0-9\._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
       var phonePattern = /^\+?[0-9]{2,8}[\/]?[0-9]+$/; // zuerst Leerzeichen und - entfernen
       
       var phoneNumber = "";
       if(!isEmpty(phone.val())) {
            phoneNumber = phone.val().replace(/\s/g, "");
            phoneNumber = phoneNumber.replace(/-/g, "");
            
            if(!phonePattern.test(phoneNumber)) {
                phone.get(0).focus();
                alert("Die von Ihnen eingegebene Telefonnummer scheint ungültig zu sein.\nBitte kontrollieren Sie Ihre Eingabe.")
                return false;
            }
            else {
                data[phone.attr("id")] = phoneNumber;
            }
       }
       
       var mailAddress = "";
       if(!isEmpty(mail.val())) {
            mailAddress = mail.val();
            
            if(!mailPattern.test(mailAddress)) {
                mail.get(0).focus();
                alert("Die von Ihnen eingegebene E-Mail-Adresse scheint ungültig zu sein.\nBitte kontrollieren Sie Ihre Eingabe.")
                return false;
            }
            else {
                data[mail.attr("id")] = mailAddress;
            }
       }
       
       $.ajax({
            url: "https://www.kreoforto.de/php/mailer.php",  // durch korrekte https url ersetzen!
            type: "POST",
            data: { info: { company: data[company.attr("id")], name: data[name.attr("id")], phoneNumber: data[phone.attr("id")], mailAddress: data[mail.attr("id")], message: data[msg.attr("id")] } },
            success: function() {
                alert("Vielen Dank für Ihre Anfrage!\nWir werden uns unverzüglich darum kümmern!");
            },
            error: function() {
                alert("Leider ist bei der Übermittlung Ihrer Anfrage ein Fehler aufgetreten.\n\n"
                      + "Bitte kontaktieren Sie uns per Mail oder rufen Sie uns an und weisen Sie uns auf diesen Fehler hin.");
            }
       });
       
       return false;
    });
    
});

function isEmpty(string) {
    
    var pattern = /[\S]+/;
    return !pattern.test(string);
}