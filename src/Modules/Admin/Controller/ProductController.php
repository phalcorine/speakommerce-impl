<?php

namespace App\Modules\Admin\Controller;

use App\Dto\Catalog\Product\UpdateProductDto;
use App\Entity\Product;
use App\Enum\SessionFlashType;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/products', name: 'admin.products.')]
class ProductController extends AbstractController
{
    private string $sectionTitle = "Manage Products";

    private ProductRepository $productRepository;
    private ValidatorInterface $validator;
    private ProductCategoryRepository $categoryRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        ProductCategoryRepository $categoryRepository,
        ValidatorInterface $validator
    )
    {
        $this->productRepository = $productRepository;
        $this->validator = $validator;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'index')]
    public function index(): Response
    {
        $products = $this->productRepository->findBy([], [
            'name'      => 'ASC'
        ]);

        $pageTitle = "List of Products";

        return $this->render('admin/products/index.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'products'          => $products
        ]);
    }

    #[Route('/details/{id}', name: 'details')]
    public function details($id)
    {
        $product = $this->productRepository->find($id);
        if(is_null($product)) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The specified product was not found..."
            );

            return $this->redirectToRoute('admin.products.index');
        }

        $pageTitle = "Product Details - " . $product->getName();

        return $this->render('admin/products/details.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'product'           => $product
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger)
    {
        $dto = new UpdateProductDto();
        if($request->request->has('categoryId')) {
            $dto->setCategoryId($request->query->getInt('categoryId'));
        }

        if($request->isMethod('POST'))
        {
            $dto = UpdateProductDto::fromRequest($request);

            $validationResult = $this->validator->validate($dto);
            if(count($validationResult) <= 0)
            {
                $category = $this->categoryRepository->find($dto->getCategoryId());

                $product = (new Product())
                    ->setName($dto->getName())
                    ->setDescription($dto->getDescription())
                    ->setPrice($dto->getPrice())
                    ->setSlug($slugger->slug($dto->getName()))
                    ->setCategory($category);

                $this->entityManager->persist($product);
                $this->entityManager->flush();

                $this->addFlash(
                    SessionFlashType::SUCCESS,
                    "Product added successfully..."
                );

                return $this->redirectToRoute('admin.products.details', [
                    'id'    => $product->getId()
                ]);
            }

            $this->addFlash(
                SessionFlashType::ERROR,
                "One or more errors occurred..."
            );

            /** @var ConstraintViolation $violation */
            foreach ($validationResult as $violation) {
                $this->addFlash(
                    SessionFlashType::ERROR,
                    $violation->getMessage()
                );
            }
        }

        $categories = $this->categoryRepository->findBy([], [
            'name'  => 'ASC'
        ]);

        $pageTitle = "Add Product";

        return $this->render('admin/products/new.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'formDto'           => $dto,
            'categories'        => $categories
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit($id, Request $request): RedirectResponse|Response
    {
        $product = $this->productRepository->find($id);
        if(is_null($product)) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The specified product was not found..."
            );

            return $this->redirectToRoute('admin.products.index');
        }

        $dto = (new UpdateProductDto())
            ->setName($product->getName())
            ->setDescription($product->getDescription())
            ->setPrice($product->getPrice())
            ->setCategoryId($product->getCategory()->getId());

        if($request->isMethod('POST'))
        {
            $dto = UpdateProductDto::fromRequest($request);

            $validationResult = $this->validator->validate($dto);
            if(count($validationResult) <= 0)
            {
                $category = $this->categoryRepository->find($dto->getCategoryId());

                $product = (new Product())
                    ->setName($dto->getName())
                    ->setDescription($dto->getDescription())
                    ->setPrice($dto->getPrice())
                    ->setSlug($slugger->slug($dto->getName()))
                    ->setCategory($category);

                $this->entityManager->persist($product);
                $this->entityManager->flush();

                $this->addFlash(
                    SessionFlashType::SUCCESS,
                    "Product added successfully..."
                );

                return $this->redirectToRoute('admin.products.details', [
                    'id'    => $product->getId()
                ]);
            }

            $this->addFlash(
                SessionFlashType::ERROR,
                "One or more errors occurred..."
            );

            /** @var ConstraintViolation $violation */
            foreach ($validationResult as $violation) {
                $this->addFlash(
                    SessionFlashType::ERROR,
                    $violation->getMessage()
                );
            }
        }

        $pageTitle = "Edit Product Details - " . $product->getName();

        return $this->render('admin/products/edit.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'formDto'           => $dto,
            'product'           => $product
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['GET', 'POST'])]
    public function delete($id, Request $request): RedirectResponse
    {
        $product = $this->productRepository->find($id);
        if($product == null) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The product specified does not exists..."
            );

            return $this->redirectToRoute('admin.products.index');
        }

        if(!$this->isCsrfTokenValid('delete_product-' . $product->getId(), $request->request->get('_form_token')))
        {
            $this->addFlash(
                SessionFlashType::ERROR,
                "Please submit the form properly"
            );

            return $this->redirectToRoute('admin.products.edit', [
                'id'    => $id
            ]);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->addFlash(
            SessionFlashType::SUCCESS,
            "Product deleted successfully..."
        );

        return $this->redirectToRoute('admin.products.index');
    }
}