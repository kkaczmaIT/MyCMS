function getSettings()
{
    let PHPVersion = document.getElementById('PHP_version');
    let fileSize = document.getElementById('limit_upload_file_size');
    let contact = document.getElementById('contact');
    let request = new XMLHttpRequest;
    let ID = window.location.href.split('/');
    ID = ID[ID.length - 1]; 
    request.open("GET", `http://localhost/BDProject/project/settings/${ID}`, true);
    request.send();
    request.onload = function()
    {
        let settings = this.responseText;
        settings = JSON.parse(settings); 
        console.log(settings);
        PHPVersion.value = settings['data']['PHP_version'];
        fileSize.value = settings['data']['limit_upload_file_size'];
        contact.value = settings['data']['contact'];    
    }
}

function createWebsite(event)
{
    event.preventDefault();
    let dataWebsite = {
        'data': {
            'title_website': document.querySelector('#title_website').value,
            'shortcut_icon_path': document.querySelector('#shortcut_icon_path').value,
            'contact': document.querySelector('#contact').value
        }
    }
    request = new XMLHttpRequest;
    request.open('POST', 'http://localhost/BDProject/project/websites/add', true);
    request.send(JSON.stringify(dataWebsite));

    request.onload = function() 
    {
        let data = JSON.parse(this.responseText);
        console.log(data);
        if(data.title_website_err != "")
            document.querySelector('#title_website').value = '';
        if(data.shortcut_icon_path_err != "")
            document.querySelector('#shortcut_icon_path').value = '';
        if(data.contact_err != "")
            document.querySelector('#contact').value = '';
        if(data.title_website_err == "" && data.shortcut_icon_path_err == "" && data.contact_err == "")
        {
            document.querySelector('#title_website').value = '';
            document.querySelector('#shortcut_icon_path').value = '';
            document.querySelector('#contact').value = '';
        }
        document.querySelector('#title_website_err').textContent = data.title_website_err;
        document.querySelector('#shortcut_icon_path_err').textContent = data.shortcut_icon_path_err;
        document.querySelector('#contact_err').textContent = data.contact_err;
        if(data.message != "")
        {
            document.querySelector('#msg-result').textContent = data.message;
            document.querySelector('#msg-result').classList.remove('d-none');
        }
        else
        {
            document.querySelector('#msg-result').classList.add('d-none');
        }
    }

}

function updateWebsiteSettings(event)
{
    event.preventDefault();
    let dataSettings = {
        'data': {
            'limit_upload_file_size': document.querySelector('#limit_upload_file_size').value,
            'contact': document.querySelector('#contact').value
        }
    }
    let ID = window.location.href;
    ID = ID.split('/');
    ID = ID[ID.length - 1];
    request = new XMLHttpRequest;
    request.open('PUT', 'http://localhost/BDProject/project/settings/edit/' + ID, true);
    request.send(JSON.stringify(dataSettings));

    request.onload = function() 
    {
        let data = JSON.parse(this.responseText);
        console.log(data);
        document.querySelector('#limit_upload_file_size_err').textContent = data.limit_upload_file_size_err_err;
        document.querySelector('#contact_err').textContent = data.contact_err;
        if(data.message != "")
        {
            document.querySelector('#msg-result').textContent = data.message;
            document.querySelector('#msg-result').classList.remove('d-none');
        }
        else
        {
            document.querySelector('#msg-result').classList.add('d-none');
        }
    }
}



let patternSettings = new RegExp('^http://localhost/BDProject/project/websites/settings/+');
if(patternSettings.test(window.location.href))
{
    console.log('Ustawienia');
    getSettings();
}