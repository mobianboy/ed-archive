<?php
namespace Eardish\Bridge\Traits;

trait Perms
{
    /**
     * @param $dto
     * @param $bitmask
     * @returns boolean
     *
     * @codeCoverageIgnore
     */
    public function hasPermission($cred, $bitmask)
    {
        define("ANON",0b0000);
        define("AUTH",0b0001);
        define("MBMR",0b0010);
        define("ADMN",0b0100);
        define("OWNR",0b1000);

        // bitmask denotes permissions required for operation
        // do a bitwise AND on the bitmask compared to the

        // if everything is set to zero, then just return true
        if(($cred == 0) && ($bitmask == 0)) {
            return true;
        }

        if($cred & $bitmask) {                // something matches if true
            if ($bitmask & AUTH) {
                if (!(AUTH & $cred)) {
                    return false;
                }
            }
            if ($bitmask & MBMR) {
                if (!(MBMR & $cred)) {
                    return false;
                }
            }
            if ($bitmask & ADMN) {
                if (!(ADMN & $cred)) {
                    return false;
                }
            }
            if ($bitmask & OWNR) {
                if(!(OWNR & $cred)) {
                    return false;
                }
            }
        }
        return true;
    }
}
