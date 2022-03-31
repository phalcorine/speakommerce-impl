<?php

namespace App\Dto\Catalog\ProductCategory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\AtLeastOneOf;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdateProductCategoryDto
{
    #[NotBlank(message: 'Name is required.')]
    #[Length(
        min: 3,
        max: 100,
        minMessage: 'Name can not be less than {{ limit }} characters',
        maxMessage: 'Name can not be more than {{ limit }} characters',
    )]
    private ?string $name = null;

//    #[AtLeastOneOf([
//        new Blank(),
//        new Length(
//            min: 3,
//            max: 100,
//            minMessage: 'Description can not be less than {{ limit }} characters',
//            maxMessage: 'Description can not be more than {{ limit }} characters',
//        ),
//    ])]
    private ?string $description = null;

    public static function fromRequest(Request $request): UpdateProductCategoryDto
    {
        return (new self())
            ->setName($request->request->get('name'))
            ->setDescription($request->request->get('description'));
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
     * @return UpdateProductCategoryDto
     */
    public function setName(?string $name): UpdateProductCategoryDto
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
     * @return UpdateProductCategoryDto
     */
    public function setDescription(?string $description): UpdateProductCategoryDto
    {
        $this->description = $description;
        return $this;
    }


}