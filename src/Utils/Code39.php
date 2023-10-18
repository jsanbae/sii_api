<?php

namespace Jsanbae\SIIAPI\Utils;

class Code39
{
    private $strDataToEncode;
    private $blnAddCheckDigit;

    public function __construct(string $_strDataToEncode, int $_blnAddCheckDigit)
    {
        $this->strDataToEncode = $_strDataToEncode;
        $this->blnAddCheckDigit = $_blnAddCheckDigit;
    }

    public function __invoke():string
    {
        return $this->Code39();
    }

    private function Code39(): string
    {
        $strDataToEncode = $this->strDataToEncode;
        $blnAddCheckDigit = $this->blnAddCheckDigit;

        $ary3of9CharSet =[];
        $strChar = "";
        $lngCheckDigit = 0;
        $lngCharIndex = 0;
        $strEncode = "";
        $strEncodeFormat = "";
        $i = 0;
        $j = 0;
        $cstrGuard = "010010100";
        $cstrPadd = "0";
        $strEncodedString = "";
        // numbers 0 to 9
        $ary3of9CharSet[0] = "000110100";
        $ary3of9CharSet[1] = "100100001";
        $ary3of9CharSet[2] = "001100001";
        $ary3of9CharSet[3] = "101100000";
        $ary3of9CharSet[4] = "000110001";
        $ary3of9CharSet[5] = "100110000";
        $ary3of9CharSet[6] = "001110000";
        $ary3of9CharSet[7] = "000100101";
        $ary3of9CharSet[8] = "100100100";
        $ary3of9CharSet[9] = "001100100";
        // letters A to Z
        $ary3of9CharSet[10] = "100001001";
        $ary3of9CharSet[11] = "001001001";
        $ary3of9CharSet[12] = "101001000";
        $ary3of9CharSet[13] = "000011001";
        $ary3of9CharSet[14] = "100011000";
        $ary3of9CharSet[15] = "001011000";
        $ary3of9CharSet[16] = "000001101";
        $ary3of9CharSet[17] = "100001100";
        $ary3of9CharSet[18] = "001001100";
        $ary3of9CharSet[19] = "000011100";
        $ary3of9CharSet[20] = "100000011";
        $ary3of9CharSet[21] = "001000011";
        $ary3of9CharSet[22] = "101000010";
        $ary3of9CharSet[23] = "000010011";
        $ary3of9CharSet[24] = "100010010";
        $ary3of9CharSet[25] = "001010010";
        $ary3of9CharSet[26] = "000000111";
        $ary3of9CharSet[27] = "100000110";
        $ary3of9CharSet[28] = "001000110";
        $ary3of9CharSet[29] = "000010110";
        $ary3of9CharSet[30] = "110000001";
        $ary3of9CharSet[31] = "011000001";
        $ary3of9CharSet[32] = "111000000";
        $ary3of9CharSet[33] = "010010001";
        $ary3of9CharSet[34] = "110010000";
        $ary3of9CharSet[35] = "011010000";
        // allowed symbols - . _ $ / + %
        $ary3of9CharSet[36] = "010000101";
        $ary3of9CharSet[37] = "110000100";
        $ary3of9CharSet[38] = "011000100";
        $ary3of9CharSet[39] = "010101000";
        $ary3of9CharSet[40] = "010100010";
        $ary3of9CharSet[41] = "010001010";
        $ary3of9CharSet[42] = "000101010";

        // validate data to encode
        // replace spaces w/ underscores
        // remove all asterisks * (we will add t
        //     hem later)
        // force upper case per spec
        while (strpos($strDataToEncode, " ") !== false) $strDataToEncode = str_replace(" ", "_", $strDataToEncode);
        while (strpos($strDataToEncode, "*") !== false) $strDataToEncode = str_replace("*", "", $strDataToEncode);
        $strDataToEncode = strtoupper($strDataToEncode);
        // encode data using character set
        // get the check digit calculation while
        // we're at it
        
         for ($i = 0; $i < strlen($strDataToEncode); $i++){
            $strChar = substr($strDataToEncode, $i, 1);
    
            if (preg_match("[a-zA-Z0-9\-._\$\/+\%]", $strChar)) {
                throw new \InvalidArgumentException("Invalid character found in data to encode");
            }
    
           switch (true) {
              case $strChar == "-": $lngCharIndex = 36; break;
              case $strChar == ".": $lngCharIndex = 37; break;
              case $strChar == "_": $lngCharIndex = 38; break;
              case $strChar == "$": $lngCharIndex = 39; break;
              case $strChar == "/": $lngCharIndex = 40; break;
              case $strChar == "+": $lngCharIndex = 41; break;
              case $strChar == "%": $lngCharIndex = 42; break;
              case is_numeric($strChar): $lngCharIndex = (int) $strChar; break;
              default: $lngCharIndex = ord($strChar) - 55; break;
            }
    
            $lngCheckDigit += $lngCharIndex;
            $strEncode .= $ary3of9CharSet[$lngCharIndex];
        }   /*¨for */
    
        // finish the check-digit
        $lngCheckDigit %= 43;
        
        // should we incorporate the check digit
        //     ?
        if ($blnAddCheckDigit != 0) $strEncode .= $ary3of9CharSet[$lngCheckDigit];
        // add start/stop characters (asterisks 
        //     "*")
        $strEncode = $cstrGuard . $strEncode . $cstrGuard;
        // now, format the output - the std aspe
        //     ct ratio is 3:1 per spec (used here)
        //- the minimum ratio is 2:1 fyi.
        // hint -- the odd/even value of the var
        //     iable "j" (found by "or-ing" by 1)
        // indicates whether a bar or space shou
        //     ld be produced. the value (1 or 0) found
        //     
        // in the string variable "strEncodeForm
        //     at" at the location of "j" indicates
        // whether the bar/space should be a wid
        //     e or narrow element in the bar code.
    
        $strEncodeLength = strlen($strEncode);
        for ($i = 0; $i < $strEncodeLength; $i += 9){
            $strEncodeFormat = substr($strEncode, $i, 9);
            for ($j = 0; $j < 9; $j++) {
                 if (($j & 1) == 1){
                    $strEncodedString .= ((substr($strEncodeFormat, $j, 1) == 1) ? "000" : "0"); 
                  }else{ 
                    $strEncodedString .= ((substr($strEncodeFormat, $j, 1) == 1) ? "111" : "1"); 
                  }
            }  /* for j */
            $strEncodedString .= $cstrPadd;
        } /* for i */

       return $strEncodedString;
    }
    
}
