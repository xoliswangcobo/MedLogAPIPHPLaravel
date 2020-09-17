<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>MedLog</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            label {
                display: block;
                font: .9rem 'Fira Sans', sans-serif;
            }

            input[type='submit'],
            label {
                margin-top: 1rem;
            }
        </style> 
    </head>
    <body>
        <div id="sc-password">
            <h1>Reset Password</h1>
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username">
            </div>

            <div>
                <label for="password">Password (8 characters minimum):</label>
                <input type="password" id="pass" name="password" minlength="8" required>
            </div>

            <input type="submit" value="Sign in">
        </div>
    </body>
</html>