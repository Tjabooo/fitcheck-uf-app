<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Invalid token</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Outfit" rel="stylesheet">
        <style>
            body {
                text-align: center;
                padding: 50px;
                font-family: "Outfit", sans-serif;
            }
            h1 {
                color: red;
            }
        </style>
    </head>
    <body>
        <h1>Invalid or expired token</h1>
        <p>Go back to <a href='{{ url('/login') }}'>login</a>.</p>
    </body>
</html>
