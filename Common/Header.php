<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" >
    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.2.0/mdb.min.css" rel="stylesheet">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css">

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>
 
    <link rel="stylesheet" href="../../css/main.css"> 
    <title>首頁</title>
</head>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            var mode = localStorage.getItem('mode');
            if (mode === 'dark') {
                document.body.classList.add('dark');
                document.getElementById('sw-checkbox').checked = true;
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            var mode = localStorage.getItem('chatbot');
            if (mode === 'chatbot-open') {
                document.getElementById('chatbot').style.display = 'block';
            }
        });
</script>
