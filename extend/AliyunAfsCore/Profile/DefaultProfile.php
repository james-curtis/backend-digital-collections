<?php

namespace AliyunAfsCore\Profile;

/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

use AliyunAfsCore\Auth\Credential;
use AliyunAfsCore\Auth\ShaHmac1Signer;
use AliyunAfsCore\Regions\Endpoint;
use AliyunAfsCore\Regions\EndpointProvider;
use AliyunAfsCore\Regions\LocationService;
use AliyunAfsCore\Regions\ProductDomain;

class DefaultProfile implements IClientProfile
{
    private static $profile;
    private static $endpoints;
    private static $credential;
    private static $regionId;
    private static $acceptFormat;

    private static $isigner;
    private static $iCredential;

    private function __construct($regionId, $credential)
    {
        self::$regionId = $regionId;
        self::$credential = $credential;
    }

    public static function getProfile($regionId, $accessKeyId, $accessSecret, $securityToken = null)
    {
        $credential = new Credential($accessKeyId, $accessSecret, $securityToken);
        self::$profile = new DefaultProfile($regionId, $credential);
        return self::$profile;
    }

    public function getSigner()
    {
        if (null == self::$isigner) {
            self::$isigner = new ShaHmac1Signer();
        }
        return self::$isigner;
    }

    public function getRegionId()
    {
        return self::$regionId;
    }

    public function getFormat()
    {
        return self::$acceptFormat;
    }

    public function getCredential()
    {
        if (null == self::$credential && null != self::$iCredential) {
            self::$credential = self::$iCredential;
        }
        return self::$credential;
    }

    public static function getEndpoints()
    {
        if (null == self::$endpoints) {
            self::$endpoints = EndpointProvider::getEndpoints();
        }
        return self::$endpoints;
    }

    public static function addEndpoint($endpointName, $regionId, $product, $domain)
    {
        if (null == self::$endpoints) {
            self::$endpoints = self::getEndpoints();
        }
        $endpoint = self::findEndpointByName($endpointName);
        if (null == $endpoint) {
            self::addEndpoint_($endpointName, $regionId, $product, $domain);
        } else {
            self::updateEndpoint($regionId, $product, $domain, $endpoint);
        }

        LocationService::addEndPoint($regionId, $product, $domain);

        EndpointProvider::setEndpoints(self::getEndpoints());
    }

    public static function findEndpointByName($endpointName)
    {
        if (!self::$endpoints) return null;
        foreach (self::$endpoints as $key => $endpoint) {
            if ($endpoint->getName() == $endpointName) {
                return $endpoint;
            }
        }
    }

    private static function addEndpoint_($endpointName, $regionId, $product, $domain)
    {
        $regionIds = array($regionId);
        $productsDomains = array(new ProductDomain($product, $domain));
        $endpoint = new Endpoint($endpointName, $regionIds, $productsDomains);
        if (self::$endpoints === null)
            self::$endpoints = [];
        self::$endpoints[] = $endpoint;
    }

    private static function updateEndpoint($regionId, $product, $domain, $endpoint)
    {
        $regionIds = $endpoint->getRegionIds();
        if (!in_array($regionId, $regionIds)) {
            array_push($regionIds, $regionId);
            $endpoint->setRegionIds($regionIds);
        }

        $productDomains = $endpoint->getProductDomains();
        if (null == self::findProductDomainAndUpdate($productDomains, $product, $domain)) {
            array_push($productDomains, new ProductDomain($product, $domain));
        }

        $endpoint->setProductDomains($productDomains);
    }

    private static function findProductDomainAndUpdate($productDomains, $product, $domain)
    {
        foreach ($productDomains as $key => $productDomain) {
            if ($productDomain->getProductName() == $product) {
                $productDomain->setDomainName($domain);
                return $productDomain;
            }
        }
        return null;
    }
}
