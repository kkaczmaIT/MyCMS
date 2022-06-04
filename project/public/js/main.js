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
        if(data.login_err != "" && data.password_err != "" && data.confirm_password_err != "")
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
        if(data.login_err != "" && data.password_err != "")
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