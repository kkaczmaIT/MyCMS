function getPages()
{
    const htmlList = document.getElementById('pages-list');
    let request = new XMLHttpRequest;
    request.open("GET", 'http://localhost/BDProject/project/pageswebs', true);
    request.send();
    request.onload = function()
    {
        let pages = this.responseText;
        pages = JSON.parse(pages);
        console.log(pages);
        for(let page = 0; page < pages['data'].length; page++)
        {
            htmlList.insertAdjacentHTML('beforeend', `<a  href="http://localhost/BDProject/project/pageswebs/pageslist/${pages['data'][page]['ID']}" class="list-group-item list-group-item-action d-flex row-direction justify-content-evenly"><span class="w-25">${pages['data'][page]['title']}</span> <span class="w-25"> >>Szczegóły</span></a>`);
        }
    }
}

function getPage()
{
   let ID = window.location.href;
   let titleField = document.getElementById('title');
   let keyphrasesField = document.getElementById('keyphrases');
   let description_metaField = document.getElementById('description_meta');
   let  contentField = document.getElementById('content');
   let footer_textField = document.getElementById('footer_text');
   let btnMenu = document.getElementById('btn-menu');
   let btnDelete = document.getElementById('btn-delete-page');
   ID = ID.split('/');
   ID = ID[ID.length - 1];
    let request = new XMLHttpRequest;
    request.open("GET", 'http://localhost/BDProject/project/pageswebs/' + ID, true);
    request.send();
    request.onload = function()
    {
        let page = this.responseText;
        console.log(page);
        page = JSON.parse(page);
        console.log(page);
        const {ID, ID_menu, content, description_meta, footer_text, keyphrases, title} = page['data'][0];
        btnMenu.href = btnMenu.href + ID_menu;
        btnDelete.dataset.id = ID;
        titleField.value = title;
        keyphrasesField.value = keyphrases;
        description_metaField.value = description_meta;
        contentField.value = content;
        footer_textField.value = footer_text;
    }
}


function deletePage(event)
{
    event.preventDefault();
    let ID = event.target.dataset.id;
    let request = new XMLHttpRequest;
    let msgField = document.getElementById('msg-result');
    request.open("DELETE", 'http://localhost/BDProject/project/pageswebs/delete/' + ID, true);
    request.send();
    request.onload = function()
    {
        let res = this.responseText;
        console.log(res);
        res = JSON.parse(res);
        if(res['status'] == 'success')
        {
            msgField.classList.remove('alert-danger');
            msgField.classList.add('alert-success'); 
            msgField.textContent = 'Usunięto pomyślnie';
            window.location.href = 'http://localhost/BDProject/project/pageswebs/pageslist';
        }
        else
        {
            msgField.classList.remove('alert-success');
            msgField.classList.add('alert-danger');            
            msgField.textContent = 'Usuwanie nie powiodło się';
        }
    }
}

function updatePage(event)
{
    event.preventDefault();
    let titleField = document.getElementById('title');
    let keyphrasesField = document.getElementById('keyphrases');
    let description_metaField = document.getElementById('description_meta');
    let  contentField = document.getElementById('content');
    let footer_textField = document.getElementById('footer_text');
    let ID = window.location.href;
    let msgField = document.getElementById('msg-result');
    ID = ID.split('/');
    ID = ID[ID.length - 1];
    let pageData = {
        'data': {
            'title': titleField.value,
            'keyphrases': keyphrasesField.value,
            'description_meta': description_metaField.value,
            'content': contentField.value,
            'footer_text': footer_textField.value
        }
    }
    console.log(ID);
    let request = new XMLHttpRequest();
    request.open("PUT", 'http://localhost/BDProject/project/pageswebs/edit/' + ID, true);
    request.send(JSON.stringify(pageData));
    request.onload = function()
    {
        let res = this.responseText;
        console.log(res);
        res = JSON.parse(res);
        if(res['status'] == 'success')
        {
            msgField.classList.remove('d-none');
            msgField.classList.remove('alert-danger');
            msgField.classList.add('alert-success'); 
            msgField.textContent = 'Zmieniono zawartość strony';
        }
        else
        {
            msgField.classList.remove('d-none');
            msgField.classList.remove('alert-success');
            msgField.classList.add('alert-danger');            
            msgField.textContent = 'Zmiana nie powiodła się';
        }
    }
}

function createPage(event)
{
    console.log('create');
    event.preventDefault();
    event.stopImmediatePropagation();
    let titleField = document.getElementById('title');
    let keyphrasesField = document.getElementById('keyphrases');
    let description_metaField = document.getElementById('description_meta');
    let  contentField = document.getElementById('content');
    let footer_textField = document.getElementById('footer_text');
    let msgField = document.getElementById('msg-result');

    let titleFieldErr = document.getElementById('title_err');
    let keyphrasesFieldErr = document.getElementById('keyphrases_err');
    let description_metaFieldErr = document.getElementById('description_meta_err');
    let  contentFieldErr = document.getElementById('content_err');
    let footer_textFieldErr = document.getElementById('footer_text_err');
    let pageData = {
        'data': {
            'title': titleField.value,
            'keyphrases': keyphrasesField.value,
            'description_meta': description_metaField.value,
            'content': contentField.value,
            'footer_text': footer_textField.value
        }
    }
    let request = new XMLHttpRequest();
    request.open("POST", 'http://localhost/BDProject/project/pageswebs/add', true);
    request.send(JSON.stringify(pageData));
    request.onload = function()
    {
        let res = this.responseText;
        console.log(res);
        res = JSON.parse(res);
        if(res['status'] == 'success')
        {
            msgField.classList.remove('d-none');
            msgField.classList.remove('alert-danger');
            msgField.classList.add('alert-success'); 
            msgField.textContent = 'Strona została utowrzona';
        }
        else
        {
            if(res['title_err'] != "")
            {
                titleFieldErr.textContent =  res['title_err'];
            }
            else
            {
                titleFieldErr.textContent =  "";
            }

            if(res['keyphrases_err'] != "")
            {
                keyphrasesFieldErr.textContent =  res['keyphrases_err'];
            }
            else
            {
                keyphrasesFieldErr.textContent =  "";
            }

            if(res['description_meta_err'] != "")
            {
                description_metaFieldErr.textContent =  res['description_meta_err'];
            }
            else
            {
                description_metaFieldErr.textContent =  "";
            }

            if(res['content_err'] != "")
            {
                contentFieldErr.textContent =  res['content_err'];
            }
            else
            {
                contentFieldErr.textContent =  "";
            }

            if(res['footer_text_err'] != "")
            {
                footer_textFieldErr.textContent =  res['footer_text_err'];
            }
            else
            {
                footer_textFieldErr.textContent =  "";
            }
            msgField.classList.remove('d-none');
            msgField.classList.remove('alert-success');
            msgField.classList.add('alert-danger');            
            msgField.textContent = 'Wystąpił błąd';
        }
    }
}

if(window.location.href  == 'http://localhost/BDProject/project/pageswebs/pageslist')
{
    console.log('pages');
    getPages();
}

let patternPage = new RegExp('^http://localhost/BDProject/project/pageswebs/pageslist/+')
if(patternPage.test(window.location.href))
{
    getPage();
}