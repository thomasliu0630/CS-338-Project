<!DOCTYPE html>
<meta charset="utf-8">
<title>Bar chart</title>
<link rel="stylesheet" type="text/css" href="./inspector.css">
<body>
<script type="module">

import define from "./index.js";
import {Runtime, Library, Inspector} from "./runtime.js";

const runtime = new Runtime();
const main = runtime.module(define, Inspector.into(document.body));

</script>

<ul>
    <a href="../index.php"><strong>Main Menu</strong></a> - Agency and Receipient Total Obligation
</ul>