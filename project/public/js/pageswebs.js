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

if(window.location.href  == 'http://localhost/BDProject/project/pageswebs/pageslist')
{
    console.log('pages');
    getPages();
}