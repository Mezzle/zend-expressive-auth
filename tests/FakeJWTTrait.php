<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\AuthTest;

use Firebase\JWT\JWT;
use Stickee\Auth\Authentication\Service;

trait FakeJWTTrait
{
    private $private_key = "-----BEGIN RSA PRIVATE KEY-----\nMIIEowIBAAKCAQEAz6hoigh7B8ptP8UsNQ4lvxr3eY042MFeFPC1H7YMlIc+RqvR\nuN1f0womvccauYEkf7Dwr19NlBVPtPm7xJqx7sKvCLykhl/R6ilzQh/8ftbc1m9z\nBL7h9UZrCstREAnfeXyI1s8traY8afWexeeP8G0nvvEnh4MztDJcvIMXTgwwaDLH\n9Gy9rANFJp41El24kFoHeQiekIS9NzeTlIlj+c2sJuwweOYwILWJfCMW2RPuAJAd\nffPDB//jBhij9/9y4lNSS4V3fdclQP20IC/mZnAb80jisS1nBpuinGVpxjztISi0\n6EaHv2PADoRuor5wF2S5d9fCJg9uAtK9+HPH4QIDAQABAoIBAQC3QOw4W/GO14H/\n09YGYBzJgFfCqfDvv/1xx1ZfzL1fWdaIcVqbpKjKyduUgJ/B8witsw1EBnuk4gW8\nNOMSRl4d0Sq8MBWVC/xog/nV8fpWSPEMP8AmAPnRBTqzhOddmwje4hq0TpMF+ny4\nGWzrQ0XQK9P2ekSjHxLCw+r45UrssNZsatTib94FwYGQLV28dPBUjCnRf5oQzVvA\nDx+qR/M3bvDyHK8RARogk4CFxB/CZBxZr0cxy3IwVKebDsmVClYpHIfGtbTQEl1j\nk6vaJ8FKmZAW94y8ou9KwmIsxDAj/8RyY5QAz8RfwiHf4Mka1OM+OV2ZKnOhqtI9\no7A+pQ3dAoGBAPx2F8HgZV4fRCfSlJhxTNf0T8ZuUFrY0kxz9Y3CAN47ePv4hW1r\n1yt7FMQSiCN3ZG+ul0oss5ywxnXO6WHvRWCtqwqqdWCM8U649edkX7hhvDYkfO92\nc2ITNc2W7aL2PtsBWE2zlGu6EhXNpTKaeX761xWML1oRd4ZGcr1Yz2KDAoGBANKR\njBlOJfmpFhra9oMqOk8tsUg38lzd0vRKecY3xnjP85kjI02SWqWUGMP9jwu7uAGr\nb293dxTPDTvfGir1yWT1m/mWJAzgWwmtsuCs3xg+UBIfVz2tCz5rfdGpO9JWg+Di\nbKcfAel0sql80xECcJXukt+Ct4TmbbjgRxmgdI7LAoGAIp+fintX65ymIEAFGRMQ\n0t0yw6gFZTAvR0Tv/E0LCW9mnEftJKCRlej/ZBSWjyako5xyKz4ONAMadLW31DKY\ndTXcK05NYrxxq7Y6I03kwsjEozhF2iGImX7A9j0owhy7ahW5Io7qYAvYxxy490Ow\nPfXw4YekRtn2Znfq7ITtjDkCgYBzOGtV6XpEK67J0Sj0yuWPL+yDHQIEqOjm/d22\nhgyXTQr5r82Ag+YQFoKatCNTA0wDteBLOS6y9z1BlqoF+epS0UahAvQFC7slB6QK\n5u1IochEsluVhxvQ8xas5BK03Nxa8OsNY50zNsUQkxoXg6NBl4NMxIVRNpmxgR/G\nuRMukwKBgAMkiM1KVZ5XOdMI2DWUBqokF8OJ/uXLLGCyvt3bq8VKGlbk618/EzWE\n335wLwtmNUErxqXekYa6+yXy3lEfT0iI5AuJ8JuDi3mTZbJ0f9NvPCBnPUGXqt9e\nQcaLCDtZlujLctGR1KoQKINAUbdO2V0Z14Baj1pfBadUr/w/ab8E\n-----END RSA PRIVATE KEY-----\n";

    private $public_key = "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz6hoigh7B8ptP8UsNQ4l\nvxr3eY042MFeFPC1H7YMlIc+RqvRuN1f0womvccauYEkf7Dwr19NlBVPtPm7xJqx\n7sKvCLykhl/R6ilzQh/8ftbc1m9zBL7h9UZrCstREAnfeXyI1s8traY8afWexeeP\n8G0nvvEnh4MztDJcvIMXTgwwaDLH9Gy9rANFJp41El24kFoHeQiekIS9NzeTlIlj\n+c2sJuwweOYwILWJfCMW2RPuAJAdffPDB//jBhij9/9y4lNSS4V3fdclQP20IC/m\nZnAb80jisS1nBpuinGVpxjztISi06EaHv2PADoRuor5wF2S5d9fCJg9uAtK9+HPH\n4QIDAQAB\n-----END PUBLIC KEY-----\n";

    public function generateFakeJWT(array $body)
    {
        $encoded = JWT::encode(
            $body,
            $this->private_key,
            Service::ALLOWED_ALGS[0],
            sha1($this->private_key)
        );

        return $encoded;
    }
}
