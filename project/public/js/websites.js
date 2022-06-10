function getWebsites()
{
    const htmlList = document.getElementById('websites-gallery');
    let request = new XMLHttpRequest;
    request.open("GET", 'http://localhost/BDProject/project/websites', true);
    request.send();
    request.onload = function()
    {
        let websites = this.responseText;
        
        websites = JSON.parse(websites); 
        
        for(let website = 0; website < websites['data'].length; website++)
        {
            htmlList.insertAdjacentHTML('beforeend', `<div class="col-4 p-2"><div class="card"><div class="card-header text-center"><h4>${websites['data'][website]['title_website']}</h4><img height="128px" width="128px" src="${websites['data'][website]['shortcut_icon_path']}" alt="ikona witryny"><div class="card-body">
            <a  href="http://localhost/BDProject/project/websites/websitespanel/${websites['data'][website]['ID']}" class="btn btn-info">Zarządzaj</a> </div> </div>`);
        }
    }
}

function getWebsiteForm()
{
    let titleWebiste = document.getElementById('title_website');
    let shortcutIcon = document.getElementById('shortcut_icon_path');
    let request = new XMLHttpRequest;
    let ID = window.location.href.split('/');
    ID = ID[ID.length - 1]; 
    request.open("GET", `http://localhost/BDProject/project/websites/${ID}`, true);
    request.send();
    request.onload = function()
    {
        let website = this.responseText;
        website = JSON.parse(website); 
        console.log(website);
        let srcIcon = website['data']['shortcut_icon_path'].split('/');
        srcIcon = srcIcon[srcIcon.length - 1];
        
        titleWebiste.value = website['data']['title_website'];
        shortcutIcon.value = srcIcon;   
    }
}

function getWebsite()
{
    const htmlList = document.getElementById('website-details');
    const websiteID =  parseInt(document.getElementById('website-id').textContent);
    const statusWebsiteBtn = document.getElementById('change-status-website');
    let settingsBtn = document.getElementById('btn-settings');
    let request = new XMLHttpRequest;
    request.open("GET", `http://localhost/BDProject/project/websites/${websiteID}`, true);
    request.send();
    request.onload = function()
    {
        let website = this.responseText;
        website = JSON.parse(website); 
        console.log(website);
        if(website['data']['message'])
        {
            htmlList.textContent = website['data']['message'];
        }
        else
        {
            const {ID, title_website, shortcut_icon_path, is_active, ID_settings} = website['data'];
            htmlList.insertAdjacentHTML('beforeend', `<div class="list-group-item">Nazwa: ${title_website}</div>
            <div class="list-group-item">Ikona witryny  <img src="${shortcut_icon_path}" width="64px" height="64px"></div>`);
            settingsBtn.href = settingsBtn.href + ID_settings;
            statusWebsiteBtn.dataset.status = is_active;
            if(is_active == 0)
            {
                statusWebsiteBtn.classList.remove('btn-danger');
                statusWebsiteBtn.classList.add('btn-success');
                statusWebsiteBtn.textContent = "Odblokuj witrynę";
            }
            else
            {
                statusWebsiteBtn.classList.remove('btn-success');
                statusWebsiteBtn.classList.add('btn-danger');
                statusWebsiteBtn.textContent = "Zablokuj witrynę";
            }
        }
        
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

function updateWebsite(event)
{
    event.preventDefault();
    let dataWebsite = {
        'data': {
            'title_website': document.querySelector('#title_website').value,
            'shortcut_icon_path': document.querySelector('#shortcut_icon_path').value
        }
    }
    const ID = document.getElementById('id-website').textContent;
    request = new XMLHttpRequest;
    request.open('PUT', 'http://localhost/BDProject/project/websites/edit/' + ID, true);
    request.send(JSON.stringify(dataWebsite));

    request.onload = function() 
    {
        let data = JSON.parse(this.responseText);
        console.log(data);
        if(data.title_website_err != "")
            document.querySelector('#title_website').value = '';
        if(data.shortcut_icon_path_err != "")
            document.querySelector('#shortcut_icon_path').value = '';
        if(data.title_website_err == "" && data.shortcut_icon_path_err == "")
        {
            document.querySelector('#title_website').value = '';
            document.querySelector('#shortcut_icon_path').value = '';
        }
        document.querySelector('#title_website_err').textContent = data.title_website_err;
        document.querySelector('#shortcut_icon_path_err').textContent = data.shortcut_icon_path_err;
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

function deleteUser(event)
{
    event.preventDefault();
    request = new XMLHttpRequest;
    request.open('DELETE', 'http://localhost/BDProject/project/users/deleteUser', true);
    request.send();

    request.onload = function() 
    {
        console.log(this.responseText);
        let data = JSON.parse(this.responseText);
        if(data.status != "success")
        {
            document.querySelector('#msg-result').classList.remove('alert-success');
            document.querySelector('#msg-result').classList.add('alert-danger');
        }
        else
        {
            document.querySelector('#msg-result').classList.add('alert-success');
            document.querySelector('#msg-result').classList.remove('alert-danger');
        }
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


function changeStatusWebsite(event)
{
    event.preventDefault();
    const statusWebsiteBtn = document.getElementById('change-status-website');
    const ID = document.getElementById('website-id').textContent;
    let status = statusWebsiteBtn.dataset.status;
    status == 0 ? status = 1 : status = 0;
    request = new XMLHttpRequest;
    request.open('PUT', 'http://localhost/BDProject/project/websites/changestatuswebsite/' + ID + '/' + status, true);
    request.send();

    request.onload = function() 
    {
        window.location.reload();
        console.log(this.responseText);
        let data = JSON.parse(this.responseText);
        if(data.status != "success")
        {
            document.querySelector('#msg-result').classList.remove('alert-success');
            document.querySelector('#msg-result').classList.add('alert-danger');
        }
        else
        {
            document.querySelector('#msg-result').classList.add('alert-success');
            document.querySelector('#msg-result').classList.remove('alert-danger');
        }
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

if(window.location.href  == 'http://localhost/BDProject/project/websites/websitespanel')
{
    console.log('panel');
    getWebsites();
}

let patternWebsite = new RegExp('^http://localhost/BDProject/project/websites/websitespanel/+');
if(patternWebsite.test(window.location.href))
{
    getWebsite();
}

let patternWebsiteEdit = new RegExp('^http://localhost/BDProject/project/websites/editwebsite/+');
if(patternWebsiteEdit.test(window.location.href))
{
    getWebsiteForm();
}