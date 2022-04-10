<?php

namespace App\Modules\Admin\Controller;

use App\Dto\Catalog\Product\UpdateProductDto;
use App\Entity\MediaFile;
use App\Entity\Product;
use App\Enum\SessionFlashType;
use App\Repository\MediaFileRepository;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    private MediaFileRepository $mediaFileRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        MediaFileRepository $mediaFileRepository,
        ProductRepository $productRepository,
        ProductCategoryRepository $categoryRepository,
        ValidatorInterface $validator
    )
    {
        $this->productRepository = $productRepository;
        $this->validator = $validator;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
        $this->mediaFileRepository = $mediaFileRepository;
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

        $mediaFiles = $this->mediaFileRepository->findBy([
            'product'   => $product
        ], [
            'name'  => 'ASC'
        ]);

        $pageTitle = "Product Details - " . $product->getName();

        return $this->render('admin/products/details.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'product'           => $product,
            'mediaFiles'        => $mediaFiles
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger, LoggerInterface $logger)
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

                /**
                 * @var UploadedFile $imageFile
                 */
                $file = $request->files->get('image_file');

                if (empty($file)) {
                    throw new \LogicException("Please select a valid image cover file...");
                }

                $originalImageFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $normalizedImageFilename = $slugger->slug($originalImageFilename);
                $finalImageName = $normalizedImageFilename . '_' . uniqid() . '.' . $file->guessClientExtension();

                try {

                    $file->move(
                        $this->getParameter('product_image_file_directory'),
                        $finalImageName
                    );

                } catch (FileException $exception) {
                    $logger->critical($exception->getMessage());
                    throw new \LogicException("An error occurred while attempting to copy the image file to storage...");
                }

                $mediaFile = (new MediaFile())
                    ->setProduct($product)
                    ->setName("Thumbnail Image")
                    ->setImageName($finalImageName);

                $this->entityManager->persist($mediaFile);

                $product->setThumbnailImage($finalImageName);
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
    public function edit($id, Request $request, SluggerInterface $slugger): RedirectResponse|Response
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

                $product
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

        $pageTitle = "Edit Product Details - " . $product->getName();

        return $this->render('admin/products/edit.html.twig', [
            'sectionTitle'      => $this->sectionTitle,
            'pageTitle'         => $pageTitle,
            'formDto'           => $dto,
            'product'           => $product,
            'categories'        => $categories,
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

    #[Route('/add-image/{productId}', name: 'add_image_to_product', methods: ['GET', 'POST'])]
    public function addImage($productId, Request $request, SluggerInterface $slugger, LoggerInterface $logger): RedirectResponse|Response
    {
        $product = $this->productRepository->find($productId);
        if($product == null) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The product specified does not exists..."
            );

            return $this->redirectToRoute('admin.products.index');
        }

        if(count($product->getImages()) >= 3) {
            $this->addFlash(
                SessionFlashType::NOTICE,
                "Sorry. You can only upload a maximum of three (3) images..."
            );
        }

        if($request->isMethod('POST'))
        {
            $name = $request->request->get('image_name');

            /**
             * @var UploadedFile $imageFile
             */
            $file = $request->files->get('image_file');

            if (empty($file)) {
                throw new \LogicException("Please select a valid image cover file...");
            }

            $originalImageFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $normalizedImageFilename = $slugger->slug($originalImageFilename);
            $finalImageName = $normalizedImageFilename . '_' . uniqid() . '.' . $file->guessClientExtension();

            try {

                $file->move(
                    $this->getParameter('product_image_file_directory'),
                    $finalImageName
                );

            } catch (FileException $exception) {
                $logger->critical($exception->getMessage());
                throw new \LogicException("An error occurred while attempting to copy the image file to storage...");
            }

            $mediaFile = (new MediaFile())
                ->setProduct($product)
                ->setName($name)
                ->setImageName($finalImageName);

            $this->entityManager->persist($mediaFile);
            $this->entityManager->flush();

            $this->addFlash(
                SessionFlashType::SUCCESS,
                "Image added successfully..."
            );

            return $this->redirectToRoute('admin.products.details', [
                'id'    => $productId
            ]);
        }

        $pageTitle = "Add Image to Product";

        return $this->render('admin/products/add_image.html.twig', [
            'pageTitle'     => $pageTitle,
            'product'       => $product
        ]);
    }

    #[Route('/remove-image-from-product/{productId}/{id}', name: 'remove_image_from_product', methods: ['GET', 'POST'])]
    public function removeImageFromProduct(int $productId, int $id): RedirectResponse
    {
        $product = $this->productRepository->find($productId);
        if($product == null) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The product specified does not exists..."
            );

            return $this->redirectToRoute('admin.products.index');
        }

        $mediaFile = $this->mediaFileRepository->findOneBy([
            'id'        => $id,
            'product'   => $product
        ]);
        if(is_null($mediaFile)) {
            $this->addFlash(
                SessionFlashType::ERROR,
                "The product image specified does not exists..."
            );

            return $this->redirectToRoute('admin.products.details', [
                'id'    => $productId
            ]);
        }

        $this->entityManager->remove($mediaFile);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin.products.details', [
            'id'    => $productId
        ]);
    }
}