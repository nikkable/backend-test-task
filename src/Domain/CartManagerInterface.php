<?php
namespace Raketa\BackendTestTask\Domain;

interface CartManagerInterface
{
    public function getCart(): Cart;
    public function saveCart(Cart $cart): void;
}