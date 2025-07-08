<?php
// nowplaying class

class lastfm_nowplaying {

    private string $api_root = "http://ws.audioscrobbler.com/2.0/";
    private string $user_agent = 'nowplaying widget - http://www.icj.me/';
    private string $api_key;
    private string $size;

    public function __construct(string $api_key, string $size = "medium") {
        if (!$api_key) {
            throw new Exception("Please set an API key.");
        }
        $this->api_key = $api_key;
        $this->size = $size;
    }

    private function is_too_long(string $string): string {
        $len = ($this->size == "medium") ? 30 : 25;

        if (strlen($string) >= $len) {
            return '<marquee direction="left" behavior="scroll" scrollamount="3">' . $string . '</marquee>';
        } else {
            return $string;
        }
    }

    private function retrieveData(string $url): string {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_USERAGENT => $this->user_agent
        ]);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function info(string $username): array {
        $recent_json = $this->retrieveData(
            $this->api_root . "?format=json&method=user.getrecenttracks&user=" . urlencode($username) . "&api_key=" . $this->api_key . "&limit=5"
        );
        $recent_data = json_decode($recent_json, true);

        if (isset($recent_data["error"])) {
            throw new Exception("Last.fm error: " . $recent_data["message"] ?? "Unknown error");
        }

        $tracks = $recent_data['recenttracks']['track'] ?? [];
        if (!isset($tracks[0])) {
            return [
                'name' => 'Nothing playing',
                'artist' => '',
                'album' => '',
                'image' => 'no_artwork.png',
                'url' => '',
                'nowplaying' => false,
                'userloved' => '',
                'playcount' => '',
                'duration' => ''
            ];
        }

        $track = $tracks[0];

        foreach ($track as $key => $item) {
            if (is_array($item) && isset($item['#text'])) {
                $track[$key] = $item['#text'];
            }
        }

        $track['image'] = $track['image'][3]['#text'] ?? 'no_artwork.png';
        $track['nowplaying'] = isset($track['@attr']['nowplaying']);
        unset($track['@attr']);

        // Additional track info
        if (!empty($track['mbid'])) {
            $info_url = $this->api_root . "?format=json&method=track.getInfo&username=" . urlencode($username) . "&api_key=" . $this->api_key . "&mbid=" . urlencode($track['mbid']);
        } else {
            $info_url = $this->api_root . "?format=json&method=track.getInfo&username=" . urlencode($username) . "&api_key=" . $this->api_key . "&artist=" . urlencode($track['artist']) . "&track=" . urlencode($track['name']) . "&autocorrect=1";
        }

        $info_data = json_decode($this->retrieveData($info_url), true);
        $extra = $info_data['track'] ?? [];

        $track = array_merge($track, $extra);
        $track['playcount'] = $track['userplaycount'] ?? 1;
        $track['duration'] = isset($track['duration']) ? gmdate("i:s", ((int) $track['duration'] / 1000)) : '?';
        $track['userloved'] = isset($track['userloved']) && $track['userloved'] == 1 ? '<strong>&#x2764;</strong>' : '';

      foreach (['artist', 'name', 'album'] as $key) {
    if (isset($track[$key])) {
        $value = $track[$key];

        if (is_array($value) && isset($value['#text'])) {
            $value = $value['#text'];
        }

    
        if (is_string($value)) {
            $track[$key] = strlen($value) > 0 ? $this->is_too_long($value) : "Unknown $key";
        } else {
            $track[$key] = "Unknown $key";
        }
    } else {
        $track[$key] = "Unknown $key";
    }
}


        unset($track['id'], $track['listeners'], $track['toptags'], $track['userplaycount']);
        return $track;
    }
}
