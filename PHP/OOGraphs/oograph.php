<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>U.S. state choropleth</title>
    <link rel="stylesheet" type="text/css" href="./inspector.css">
</head>
<body>
    <div id="content">
        <script type="module">
            import define from "./index.js";
            import {Runtime, Library, Inspector} from "./runtime.js";

            const runtime = new Runtime();
            const main = runtime.module(define, Inspector.into(document.body));
        </script>
    </div>
</body>
</html>
<ul>
    <a href="../index.php"><strong>Main Menu</strong></a> - Compare Total Outlayed and Obligated Amount
</ul>