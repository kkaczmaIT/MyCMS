function registration(event)
{
    event.preventDefault();
    let dataUser = {
        'data': {
            'login': document.querySelector('#login').value,
            'password': document.querySelector('#password').value,
            'password_confirm': document.querySelector('#password_confirm').value
        }
    }
    request = new XMLHttpRequest;
    request.open('POST', 'http://localhost/BDProject/project/users/registerUser', true);
    request.send(JSON.stringify(dataUser));

    request.onload = function() 
    {
        let data = JSON.parse(this.responseText);
        if(data.login_err != "")
            document.querySelector('#login').value = '';
        if(data.password_err != "")
            document.querySelector('#password').value = '';
        if(data.confirm_password_err != "")
            document.querySelector('#password_confirm').value = '';
        if(data.login_err == "" && data.password_err == "" && data.confirm_password_err == "")
        {
            document.querySelector('#login').value = '';
            document.querySelector('#password').value = '';
            document.querySelector('#password_confirm').value = '';
        }
        document.querySelector('#login_err').textContent = data.login_err;
        document.querySelector('#password_err').textContent = data.password_err;
        document.querySelector('#password_confirm_err').textContent = data.confirm_password_err;
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

function loginU(event)
{
    event.stopImmediatePropagation();
    event.preventDefault();
    let dataUser = {
        'data': {
            'login': document.querySelector('#login').value,
            'password': document.querySelector('#password').value
        }
    }
    request = new XMLHttpRequest;
    request.open('POST', 'http://localhost/BDProject/project/users/login', true);
    request.send(JSON.stringify(dataUser));

    request.onload = function() 
    {
        console.log(this.responseText);
        let data = JSON.parse(this.responseText);
        if(data.login_err != "")
            document.querySelector('#login').value = '';
        if(data.password_err != "")
            document.querySelector('#password').value = '';
        if(data.login_err == "" && data.password_err == "")
        {
            document.querySelector('#login').value = '';
            document.querySelector('#password').value = '';
        }
        document.querySelector('#login_err').textContent = data.login_err;
        document.querySelector('#password_err').textContent = data.password_err;
        if(data.message != "")
        {
            document.querySelector('#msg-result').textContent = data.message;
            if(data.type == 'success')
            {
                document.querySelector('#msg-result').classList.remove('alert-danger');
                document.querySelector('#msg-result').classList.add('alert-success');
                setTimeout(() => window.location.reload(), 1000);
            }
            else
            {
                document.querySelector('#msg-result').classList.remove('alert-success');
                document.querySelector('#msg-result').classList.add('alert-danger');
            }
            document.querySelector('#msg-result').classList.remove('d-none');
        }
        else
        {
            document.querySelector('#msg-result').classList.add('d-none');
        }
    }

}

function getUsers()
{
    const htmlList = document.getElementById('users-list');
    let request = new XMLHttpRequest;
    request.open("GET", 'http://localhost/BDProject/project/users', true);
    request.send();
    request.onload = function()
    {
        let users = this.responseText;
        users = JSON.parse(users); 
        for(let user = 0; user < users['data'][0].length; user++)
        {
            htmlList.insertAdjacentHTML('beforeend', `<a  href="http://localhost/BDProject/project/users/userslist/${users['data'][0][user]['ID']}" class="list-group-item list-group-item-action d-flex row-direction justify-content-evenly"><span class="w-25">${users['data'][0][user]['loginU']}</span> <span class="w-25"> >>Szczegóły</span></a>`);
        }
    }
}

function getUser()
{
    const htmlList = document.getElementById('user-details');
    const userID =  parseInt(document.getElementById('user-id').textContent);
    let request = new XMLHttpRequest;
    request.open("GET", `http://localhost/BDProject/project/users/${userID}`, true);
    request.send();
    request.onload = function()
    {
        let user = this.responseText;
        user = JSON.parse(user); 
        const {ID, loginU} = user['data'];
        htmlList.insertAdjacentHTML('beforeend', `<div class="list-group-item">login username: ${loginU}</div>`);
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

if(window.location.href  == 'http://localhost/BDProject/project/users/userslist')
{
    getUsers();
}

let pattern = new RegExp('^http://localhost/BDProject/project/users/userslist/+')
if(pattern.test(window.location.href))
{
    getUser();
}