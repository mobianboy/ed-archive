<?php

// Artist Profile Page Request
$data1 = '{
    "action": {
        "route": "/artist/1922",
        "priority": 10
    },
    "auth": {
        "user": "Bonjovi",
        "pass": "123456"
    }
}';

// Artist Profile Update Request
$data2 = '{
    "action": {
        "route": "/artist/1922",
        "priority": 10
    },
    "data": {
        "bio": "We like our hair long, and our rock stars old.",
        "website": "http://www.facebook.com/"
    }
}';

// Artist Profile Update Request
$data2andahalf = '{
    "action": {
        "route": "/artist/1922",
        "priority": 10
    },
    "data": {
        "bio": "We like our hair long, and our rock stars old.",
        "website": "http://www.facebook.com/"
    },
    "auth": {
        "user": "Bonjovi",
        "pass": "123456"
    }
}';

// Artist Track Page Request
$data3 = '{
    "action": {
        "route": "/artist/1922/discography",
        "priority": 10
    }
}';

//Album Tracks Page Request
$data4 = '{
    "action": {
        "route": "/album/524",
        "priority": 10
    }
}';

////Send to API
$client = fsockopen("localhost", 80);
fwrite($client, $data2);
while (!feof($client)) {
    echo fgets($client, 128);
}
fclose($client);
