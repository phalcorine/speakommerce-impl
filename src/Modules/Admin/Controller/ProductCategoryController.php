<?php

namespace App\Modules\Admin\Controller;

use App\Dto\Catalog\ProductCategory\UpdateProductCategoryDto;
use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Enum\SessionFlashType;
use App\Exception\MultipleLogicException;
use App\Repository\ProductCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/product-categories', name: 'admin.product_categories.')]
class ProductCategoryController extends AbstractController
{
    private string $sectionTitle = "Manage Product Categories";

    private ProductCategoryRepository $categoryRepository;
    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductCategoryRepository $categoryRepository,
        ValidatorInterface $validator
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findBy([], [
            'name'  => 'ASC'
        ]);

        $pageTitle = "Product Categories";

        return $this->render('admin/product_categories/index.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'categories'        => $categories,
        ]);
    }

    #[Route('/details/{id}', name: 'details', methods: ['GET'])]
    public function details($id)
    {
        $category = $this->categoryRepository->find($id);
        if($category == null) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The product category specified does not exists..."
            );

            return $this->redirectToRoute('admin.product_categories.index');
        }

        /** @var Product[] $products */
        $products = $category->getProducts();

        $pageTitle = "Category Details: " . $category->getName();

        return $this->render('admin/product_categories/details.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'category'          => $category,
            'products'          => $products
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger)
    {
        $dto = new UpdateProductCategoryDto();

        if($request->isMethod('POST'))
        {
            $dto = UpdateProductCategoryDto::fromRequest($request);

            $validationResult = $this->validator->validate($dto);
            if(count($validationResult) > 0) {
//                $errors = [];

                $this->addFlash(
                    SessionFlashType::ERROR,
                    "One or more errors occurred..."
                );

                /** @var ConstraintViolationInterface $validation */
                foreach ($validationResult as $validation) {
                    $this->addFlash(
                        SessionFlashType::ERROR,
                        $validation->getMessage()
                    );
                }
            }

            $category = (new ProductCategory())
                ->setName($dto->getName())
                ->setDescription($dto->getDescription())
                ->setSlug($slugger->slug($dto->getName()));

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin.product_categories.details', [
                'id'    => $category->getId()
            ]);
        }

        $pageTitle = "Create a Product Category";

        return $this->render('admin/product_categories/new.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'formDto'           => $dto,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit($id, Request $request, SluggerInterface $slugger): RedirectResponse|Response
    {
        $category = $this->categoryRepository->find($id);
        if($category == null) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The product category specified does not exists..."
            );

            return $this->redirectToRoute('admin.product_categories.index');
        }

        $dto = (new UpdateProductCategoryDto())
            ->setName($category->getName())
            ->setDescription($category->getDescription());

        if($request->isMethod('POST'))
        {
            $dto = UpdateProductCategoryDto::fromRequest($request);

            $validationResult = $this->validator->validate($dto);
            if(count($validationResult) > 0) {
//                $errors = [];

                $this->addFlash(
                    SessionFlashType::ERROR,
                    "One or more errors occurred..."
                );

                /** @var ConstraintViolationInterface $validation */
                foreach ($validationResult as $validation) {
                    $this->addFlash(
                        SessionFlashType::ERROR,
                        $validation->getMessage()
                    );
                }
            }

            $category
                ->setName($dto->getName())
                ->setDescription($dto->getDescription())
                ->setSlug($slugger->slug($dto->getName()));

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin.product_categories.details', [
                'id'    => $category->getId()
            ]);
        }

        $pageTitle = "Update a Product Category";

        return $this->render('admin/product_categories/edit.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'formDto'           => $dto,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['GET', 'POST'])]
    public function delete($id, Request $request): RedirectResponse
    {
        $category = $this->categoryRepository->find($id);
        if($category == null) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The product category specified does not exists..."
            );

            return $this->redirectToRoute('admin.product_categories.index');
        }

        if(!$this->isCsrfTokenValid('delete_category-' . $category->getId(), $request->request->get('_form_token')))
        {
            $this->addFlash(
                SessionFlashType::ERROR,
                "Please submit the form properly"
            );

            return $this->redirectToRoute('admin.product_categories.edit', [
                'id'    => $id
            ]);
        }

        if($category->getProducts()->count() > 0) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "Can not delete category as one or more products are dependent on it..."
            );

            return $this->redirectToRoute('admin.product_categories.edit', [
                'id'    => $id
            ]);
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        $this->addFlash(
            SessionFlashType::SUCCESS,
            "Category deleted successfully..."
        );

        return $this->redirectToRoute('admin.product_categories.index');
    }
}