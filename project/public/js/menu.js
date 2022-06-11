function getMenu()
{
   let ID = window.location.href;
   let levelMenuField = document.getElementById('level_menu');
   let textLinkField = document.getElementById('text_link');
   let hrefField = document.getElementById('href');
   let depthField = document.getElementById('depth');
   let orderItemField = document.getElementById('order_item');
   let listGroup = document.getElementById('list-links');
   ID = ID.split('/');
   ID = ID[ID.length - 1];
    let request = new XMLHttpRequest;
    request.open("GET", 'http://localhost/BDProject/project/menus/' + ID, true);
    request.send();
    request.onload = function()
    {
        let menu = this.responseText;
        console.log(menu);
        menu = JSON.parse(menu);
        console.log(menu);
        const {ID, level_menu} = menu['data'][0];
        levelMenuField.value = level_menu;
        for(let listitem = 0; listitem < menu['data']['listitem'].length; listitem++)
        {
            let {ID, ID_menu, text_link, href, depth, order_item} = menu['data']['listitem'][listitem];
            listGroup.insertAdjacentHTML('beforeend', `<a id="linkitem-${ID}" data-text_link="${text_link}" data-href="${href}" data-depth="${depth}" data-order_item="${order_item}" href="http://localhost/BDProject/project/menus/linkitem/${ID}" class="list-group-item list-group-item-action d-flex row-direction justify-content-evenly"><span class="w-25">${text_link}</span> <span class="w-25"> >>Szczegóły</span></a>`);
        }
        // btnMenu.href = btnMenu.href + ID_menu;
        // btnDelete.dataset.id = ID;
        // titleField.value = title;
        // keyphrasesField.value = keyphrases;
        // description_metaField.value = description_meta;
        // contentField.value = content;
        // footer_textField.value = footer_text;
    }
}

function getListItem()
{
    let ID = window.location.href;
    let textLinkField = document.getElementById('text_link');
    let hrefField = document.getElementById('href');
    let depthField = document.getElementById('depth');
    let orderItemField = document.getElementById('order_item');
    let btnDelete = document.getElementById('btn-delete-listitem');
    ID = ID.split('/');
    ID = ID[ID.length - 1];
     let request = new XMLHttpRequest;
     request.open("GET", 'http://localhost/BDProject/project/menus/getlinkitem/' + ID, true);
     request.send();
     request.onload = function()
     {
         let menu = this.responseText;
         console.log(menu);
         menu = JSON.parse(menu);
         console.log(menu);

            let {ID, ID_menu, text_link, href, depth, order_item} = menu['data'][0];
            btnDelete.dataset.idmenu = ID_menu;
            textLinkField.value = text_link;
            hrefField.value = href;
            depthField.value = depth;
            orderItemField.value = order_item;
     }
}

function createLink(event)
{
    event.preventDefault();
    event.stopImmediatePropagation();
    let ID = window.location.href;
    let textLinkField = document.getElementById('text_link');
    let hrefField = document.getElementById('href');
    let depthField = document.getElementById('depth');
    let orderItemField = document.getElementById('order_item');

    let msgResult = document.getElementById('msg-result');
    let textLinkFieldErr = document.getElementById('text_link_err');
    let hrefFieldErr = document.getElementById('href_err');
    let depthFieldErr = document.getElementById('depth_err');
    let orderItemFieldErr = document.getElementById('order_item_err');
    ID = ID.split('/');
    ID = ID[ID.length - 1];

    let LinkData = {
        'data': {
            'text_link': textLinkField.value,
            'href': hrefField.value,
            'depth': depthField.value,
            'order_item': orderItemField.value
        }
    }
    console.log(LinkData);
     let request = new XMLHttpRequest;
     request.open("POST", 'http://localhost/BDProject/project/menus/addlinkitem', true);
     request.send(JSON.stringify(LinkData));
     request.onload = function()
     {
         let linkItem = this.responseText;
         console.log(linkItem);
         linkItem = JSON.parse(linkItem);
         console.log(linkItem);


         if(linkItem['status'] == 'failed')
         {
             msgResult.classList.add('alert-danger');
             msgResult.classList.remove('d-none');
             msgResult.classList.remove('alert-success');
             msgResult.textContent = linkItem['message'];
             if(linkItem['text_link_err'] != "")
             {
                 textLinkFieldErr.textContent = linkItem['text_link_err'];
             }

             if(linkItem['href_err'] != "")
             {
                 hrefFieldErr.textContent = linkItem['href_err'];
             }

             if(linkItem['depth_err'] != "")
             {
                 depthFieldErr.textContent = linkItem['depth_err'];
             }

             if(linkItem['order_item_err'] != "")
             {
                 orderItemFieldErr.textContent = linkItem['order_item_err'];
             }
         }
         else if(linkItem['status'] == 'success')
         {
            msgResult.classList.remove('alert-danger');
            msgResult.classList.remove('d-none');
            msgResult.classList.add('alert-success');
            msgResult.textContent = linkItem['message'];
            if(linkItem['text_link_err'] == "")
            {
                textLinkFieldErr.textContent = "";
            }

            if(linkItem['href_err'] == "")
            {
                hrefFieldErr.textContent = "";
            }

            if(linkItem['depth_err'] == "")
            {
                depthFieldErr.textContent = "";
            }

            if(linkItem['order_item_err'] == "")
            {
                orderItemFieldErr.textContent = "";
            }
            window.location.reload();
         }


     }
}

function editLink(event)
{
    event.preventDefault();
    event.stopImmediatePropagation();
    let ID = window.location.href;
    let textLinkField = document.getElementById('text_link');
    let hrefField = document.getElementById('href');
    let depthField = document.getElementById('depth');
    let orderItemField = document.getElementById('order_item');

    let msgResult = document.getElementById('msg-result');
    let textLinkFieldErr = document.getElementById('text_link_err');
    let hrefFieldErr = document.getElementById('href_err');
    let depthFieldErr = document.getElementById('depth_err');
    let orderItemFieldErr = document.getElementById('order_item_err');
    ID = ID.split('/');
    ID = ID[ID.length - 1];

    let LinkData = {
        'data': {
            'text_link': textLinkField.value,
            'href': hrefField.value,
            'depth': depthField.value,
            'order_item': orderItemField.value
        }
    }
    console.log(LinkData);
     let request = new XMLHttpRequest;
     request.open("PUT", 'http://localhost/BDProject/project/menus/editlinkitem/' + ID, true);
     request.send(JSON.stringify(LinkData));
     request.onload = function()
     {
         let linkItem = this.responseText;
         console.log(linkItem);
         linkItem = JSON.parse(linkItem);
         console.log(linkItem);


         if(linkItem['status'] == 'failed')
         {
             msgResult.classList.add('alert-danger');
             msgResult.classList.remove('d-none');
             msgResult.classList.remove('alert-success');
             msgResult.textContent = linkItem['message'];
             if(linkItem['text_link_err'] != "")
             {
                 textLinkFieldErr.textContent = linkItem['text_link_err'];
             }

             if(linkItem['href_err'] != "")
             {
                 hrefFieldErr.textContent = linkItem['href_err'];
             }

             if(linkItem['depth_err'] != "")
             {
                 depthFieldErr.textContent = linkItem['depth_err'];
             }

             if(linkItem['order_item_err'] != "")
             {
                 orderItemFieldErr.textContent = linkItem['order_item_err'];
             }
         }
         else if(linkItem['status'] == 'success')
         {
            msgResult.classList.remove('alert-danger');
            msgResult.classList.remove('d-none');
            msgResult.classList.add('alert-success');
            msgResult.textContent = linkItem['message'];
            if(linkItem['text_link_err'] == "")
            {
                textLinkFieldErr.textContent = "";
            }

            if(linkItem['href_err'] == "")
            {
                hrefFieldErr.textContent = "";
            }

            if(linkItem['depth_err'] == "")
            {
                depthFieldErr.textContent = "";
            }

            if(linkItem['order_item_err'] == "")
            {
                orderItemFieldErr.textContent = "";
            }
         }


     }
}

function deleteListItem(event)
{

    event.preventDefault();
    let ID = window.location.href;
    ID = ID.split('/');
    ID = ID[ID.length - 1]; 
    console.log(ID);
    let request = new XMLHttpRequest;
    let msgField = document.getElementById('msg-result');
    let btnDelete = document.getElementById('btn-delete-listitem');
    request.open("DELETE", 'http://localhost/BDProject/project/menus/deletelistitem/' + ID, true);
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
            window.location.href = 'http://localhost/BDProject/project/menus/menupanel/' + btnDelete.dataset.idmenu;
        }
        else
        {
            msgField.classList.remove('alert-success');
            msgField.classList.add('alert-danger');            
            msgField.textContent = 'Usuwanie nie powiodło się';
        }
    }
}

let patternMenu = new RegExp('^http://localhost/BDProject/project/menus/menupanel/+')
if(patternMenu.test(window.location.href))
{
    getMenu();
}

let patternLinkItem = new RegExp('^http://localhost/BDProject/project/menus/linkitem/+')
if(patternLinkItem.test(window.location.href))
{
    getListItem();
}