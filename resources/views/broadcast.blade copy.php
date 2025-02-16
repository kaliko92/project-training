{{-- <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Broadcast Example</title>
    </head>
    <body>
        <h1>Broadcast Example</h1>
        <div id="messages"></div>

        <script src="{{ asset('js/app.js') }}"></script>
        <script>
            window.Echo.channel('public-channel')
                .listen('MessageSent', (e) => {
                    const messages = document.getElementById('messages');
                    messages.innerHTML += `<p>${e.message}</p>`;
                });
        </script>
    </body>
</html> --}}




<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Broadcast Example</title>
        <style>
            #messages {
                margin-top: 20px;
                padding: 10px;
                border: 1px solid #ccc;
                height: 200px;
                overflow-y: scroll;
            }
        </style>
    </head>
    <body>
        <h1>Broadcast Example</h1>

        <!-- Form to send messages -->
        <form id="messageForm">
            <input type="text" id="messageInput" placeholder="Enter your message">
            <button type="submit">Send</button>
        </form>

        <!-- Display broadcasted messages -->
        <div id="messages"></div>

        <!-- Include Laravel Echo and Pusher -->
        <script src="{{ asset('js/app.js') }}"></script>
        <script>
            // Listen for form submission
            document.getElementById('messageForm').addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent the form from submitting

                const message = document.getElementById('messageInput').value;

                // Send the message to the server via AJAX
                fetch('/broadcast', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data); // Log the response
                    document.getElementById('messageInput').value = ''; // Clear the input field
                });
            });

            // Listen for broadcasted messages
            window.Echo.channel('public-channel')
                .listen('MessageSent', (e) => {
                    const messages = document.getElementById('messages');
                    messages.innerHTML += `<p><strong>New Message:</strong> ${e.message}</p>`;
                    messages.scrollTop = messages.scrollHeight; // Auto-scroll to the bottom
                });
        </script>
    </body>
</html>