<?php

namespace App\Dto\Catalog\Product;

use Symfony\Component\HttpFoundation\Request;

class UpdateProductDto
{
    private ?string $name;

    private ?string $description;

    private ?int $price;

    private ?int $categoryId;

    public static function fromRequest(Request $request): UpdateProductDto
    {
        return (new self())
            ->setName($request->request->get('name'))
            ->setDescription($request->request->get('description'))
            ->setPrice($request->request->getInt('price'))
            ->setCategoryId($request->request->getInt('category_id'));
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return UpdateProductDto
     */
    public function setName(?string $name): UpdateProductDto
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return UpdateProductDto
     */
    public function setDescription(?string $description): UpdateProductDto
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int|null $price
     * @return UpdateProductDto
     */
    public function setPrice(?int $price): UpdateProductDto
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    /**
     * @param int|null $categoryId
     * @return UpdateProductDto
     */
    public function setCategoryId(?int $categoryId): UpdateProductDto
    {
        $this->categoryId = $categoryId;
        return $this;
    }
}