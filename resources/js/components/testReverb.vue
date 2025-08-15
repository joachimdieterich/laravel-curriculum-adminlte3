<template>
    <label>OrderId:<input id="test"></label>
    <button onclick="start()">Start</button>
    <!--script>
        /*function start() {
            // Create a new WebSocket connection
            //const socket = new WebSocket('http://127.0.0.1:8080/app/base64:9KopJPXvmz1qNDDJyJ4FDYKfBQk+aA1mUQ8ofmigMFY=?protocol=7&client=js&version=8.4.0&flash=false');

            const fruits = [
                //'ws://127.0.0.1:8000/app/base64:9KopJPXvmz1qNDDJyJ4FDYKfBQk+aA1mUQ8ofmigMFY=?protocol=7&client=js&version=8.4.0&flash=false',
                //'ws://localhost:8000/app/base64:9KopJPXvmz1qNDDJyJ4FDYKfBQk+aA1mUQ8ofmigMFY=?protocol=7&client=js&version=8.4.0&flash=false',
                //'ws://127.0.0.1:8080/app/base64:9KopJPXvmz1qNDDJyJ4FDYKfBQk+aA1mUQ8ofmigMFY=?protocol=7&client=js&version=8.4.0&flash=false',
                'ws://localhost:6001/app/base64:9KopJPXvmz1qNDDJyJ4FDYKfBQk+aA1mUQ8ofmigMFY=?protocol=7&client=js&version=8.4.0&flash=false',
                //'ws://0.0.0.0:8080/app/base64:9KopJPXvmz1qNDDJyJ4FDYKfBQk+aA1mUQ8ofmigMFY=?protocol=7&client=js&version=8.4.0&flash=false',
                //'http://127.0.0.1:8000/app/base64:9KopJPXvmz1qNDDJyJ4FDYKfBQk+aA1mUQ8ofmigMFY=?protocol=7&client=js&version=8.4.0&flash=false',
                //'http://localhost:8000/app/base64:9KopJPXvmz1qNDDJyJ4FDYKfBQk+aA1mUQ8ofmigMFY=?protocol=7&client=js&version=8.4.0&flash=false',
                //'http://127.0.0.1:8080/app/base64:9KopJPXvmz1qNDDJyJ4FDYKfBQk+aA1mUQ8ofmigMFY=?protocol=7&client=js&version=8.4.0&flash=false',
                //'http://localhost:8080/app/base64:9KopJPXvmz1qNDDJyJ4FDYKfBQk+aA1mUQ8ofmigMFY=?protocol=7&client=js&version=8.4.0&flash=false',
                //'http://0.0.0.0:8080/app/base64:9KopJPXvmz1qNDDJyJ4FDYKfBQk+aA1mUQ8ofmigMFY=?protocol=7&client=js&version=8.4.0&flash=false',
            ];

            fruits.forEach(function (url, index) {
                const socket = new WebSocket(url);

                // Handle connection open
                socket.onopen = (event) => {
                    console.log(index + 'Connection established');
                    console.log(event);

                    // Send a message
                    socket.send(index + 'Hello, WebSocket server!');
                };

                // Handle messages
                socket.onmessage = (event) => {
                    console.log(index + 'Message received:', event.data);
                    console.log(event);

                    // Close the connection after receiving a message
                    //socket.close(1000, 'Work complete');
                };

                // Handle errors
                socket.onerror = (event) => {
                    console.error(index + 'WebSocket error:', event);
                };

                // Handle connection close
                socket.onclose = (event) => {
                    console.log(index + 'Connection closed');
                    console.log(event);
                };
            });

        }*/

        /*new Promise(resolve => setTimeout(resolve, 1000)).then(function () {
            console.log(1);
            Echo.private(`orders.{{ $order->orderId }}`)
                .listen('OrderShipmentStatusUpdated', (e) => {
                    console.log(e.order);
                    //$('#test').val(e.order.orderId);
                });
        });*/

        /*function start()
        {
            Echo.channel('orders')
                .listen('.order.shipped', (e, data) => {
                    console.log(e);
                    console.log(data);
                    //console.log(e.order);
                    //$('#test').val(e.order.orderId);
                });
        }*/

        document.onreadystatechange = function () {
            if (document.readyState === 'complete') {
                Echo.channel('orders')
                    .listen('.order.shipped', (e) => {
                        $('#test').val(e.order.orderId);
                    });
            }
        }
    </script-->
</template>

<script lang="ts">
export default {
    name: "test-reverb",
    methods: {
        startPusher() {
            this.$echo.channel('orders')
                .listen('.order.shipped', (payload: any) => {
                    console.log(payload);
                });
        },
    },
    mounted() {
        this.startPusher();
    }
};
</script>
