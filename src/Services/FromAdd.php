<?php

namespace App\Services;
use Symfony\Component\HttpFoundation\Request;

class FromAdd
{
    public function getLastPage(Request $request){
        $lastUrl = $request->getSession()->get('lastUrl');
        $thisUrl = $request->getSession()->get('thiUrl');
        if ($lastUrl == null || $thisUrl != $request->getPathInfo()){
            $lastUrl = $request->headers->get('referer');
            $request->getSession()->set('lastUrl',$lastUrl);
            $request->getSession()->set('thiUrl',$request->getPathInfo());
        }
        return $lastUrl;
    }

}