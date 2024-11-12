<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit layout</title>
    <style>
        #outline {
            width: 250px;
            height: 200px;
            padding: 30px;
            border: 1px solid;
            position: relative;
        }

        img {
            width: 50px;
            height: 50px;
            position: absolute;
        }
    </style>
    <script>
        function allowDrop(event) {
            event.preventDefault();
        }

        function drag(event) {
            event.dataTransfer.setData("text", event.target.id);
        }

        function drop(event) {
            event.preventDefault();
            var fetch_data = event.dataTransfer.getData("text");
            var dragged_element = document.getElementById(fetch_data);
            var rect = event.target.getBoundingClientRect();
            var x = event.clientX - rect.left - dragged_element.width / 2;
            var y = event.clientY - rect.top - dragged_element.height / 2;

            dragged_element.style.left = x + "px";
            dragged_element.style.top = y + "px";
            event.target.appendChild(dragged_element);
        }
    </script>
</head>

<body>
    <h2>Edit layout</h2>
    <div id="outline"
        ondrop="drop(event)"
        ondragover="allowDrop(event)">
    </div>
    <br>
    <img src="../images/2-table.svg" alt="2_table" id="2_table" draggable="true" ondragstart="drag(event)" width="50" height="50">
    <img src="../images/4-table.svg" alt="4_table" id="4_table" draggable="true" ondragstart="drag(event)" width="50" height="50">
    <img src="../images/8-table.svg" alt="8_table" id="8_table" draggable="true" ondragstart="drag(event)" width="50" height="50">
</body>

</html>