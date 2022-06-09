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
            htmlList.insertAdjacentHTML('beforeend', `<div class="col-4"><div class="card"><div class="card-header text-center"><h4>${websites['data'][website]['title_website']}</h4><img height="128px" width="128px" src="${websites['data'][website]['shortcut_icon_path']}" alt="ikona witryny"><div class="card-body">
            <a  href="http://localhost/BDProject/project/websites/websitespanel/${websites['data'][website]['ID']}" class="btn btn-info">Zarządzaj</a> </div> </div>`);
        }
    }
}

function getWebsite()
{
    const htmlList = document.getElementById('website-details');
    const websiteID =  parseInt(document.getElementById('website-id').textContent);
    const statusWebsiteBtn = document.getElementById('change-status-website');
    let request = new XMLHttpRequest;
    request.open("GET", `http://localhost/BDProject/project/websites/${websiteID}`, true);
    request.send();
    request.onload = function()
    {
        let website = this.responseText;
        website = JSON.parse(website); 
        console.log(website);
        const {ID, title_website, shortcut_icon_path, is_active} = website['data'];
        htmlList.insertAdjacentHTML('beforeend', `<div class="list-group-item">Nazwa: ${title_website}</div>
        <div class="list-group-item">Ikona witryny  <img src="${shortcut_icon_path}" width="64px" height="64px"></div>`);
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

function updateUser(event)
{
    event.preventDefault();
    let dataUser = {
        'data': {
            'password': document.querySelector('#password').value,
            'password_confirm': document.querySelector('#password_confirm').value
        }
    }
    request = new XMLHttpRequest;
    request.open('PUT', 'http://localhost/BDProject/project/users/editUser', true);
    request.send(JSON.stringify(dataUser));

    request.onload = function() 
    {
        console.log(this.responseText);
        let data = JSON.parse(this.responseText);
        if(data.password_err != "")
            document.querySelector('#password').value = '';
        if(data.confirm_password_err != "")
            document.querySelector('#password_confirm').value = '';
        if(data.password_err == "" && data.confirm_password_err == "")
        {
            document.querySelector('#password').value = '';
            document.querySelector('#password_confirm').value = '';
        }
        document.querySelector('#password_err').textContent = data.password_err;
        document.querySelector('#password_confirm_err').textContent = data.confirm_password_err;
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