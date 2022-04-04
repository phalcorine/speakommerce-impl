<?php

namespace App\Dto\Catalog\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class UpdateProductDto
{
    #[NotBlank(message: "Name is required", normalizer: 'trim')]
    #[Length(
        min: 3,
        max: 100,
        normalizer: 'trim',
        minMessage: "Product Name can not be less than {{ limit }} characters...",
        maxMessage: "Product Name can not be more than {{ limit }} characters..."
    )]
    private ?string $name = null;

    #[NotBlank(message: "Description is required...", normalizer: 'trim')]
    #[Length(
        min: 20,
        max: 100,
        normalizer: 'trim',
        minMessage: "Description can not be less than {{ limit }} characters...",
        maxMessage: "Description can not be more than {{ limit }} characters..."
    )]
    private ?string $description = null;

    #[Positive(message: 'Price can not be less than 0...')]
    private ?int $price = 0;

    #[NotBlank(message: "Category is required...")]
    private ?int $categoryId = null;

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