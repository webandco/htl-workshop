<?php
namespace App\Controller;

use App\Entity\Cat;
use App\Form\CatFormType;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use AlanCaptcha\Php\AlanApi;

class CreateCatController extends AbstractController
{

    public function __construct(
        #[Autowire('%cat_pictures_directory%')] private readonly string $catPicturesDirectory
    ) {}

    #[Route('/create-cat', name: 'app_create_cat')]
    public function createCat(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
    ): Response
    {
        $cat = new Cat();
        $form = $this->createForm(CatFormType::class, $cat);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $alanApi = new AlanApi();
            $alanApiKey = $this->getParameter('alancaptcha_api_key');

            try {
                $captchaValid = $alanApi->widgetValidate($alanApiKey, $request->request->get('alan-solution'));
            } catch (\Exception $e) {
                $captchaValid = false;
            }

            if (!$captchaValid) {
                $this->addFlash('danger', 'Captcha validation failed.');
                return $this->render('page/create_cat.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('picture')->getData();

            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                try {
                    if (!is_dir($this->catPicturesDirectory)) {
                        mkdir($this->catPicturesDirectory, 0775, true);
                    }
                    $pictureFile->move($this->catPicturesDirectory, $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Picture upload failed. Please try again.');
                }

                $cat->setPictureFilename($newFilename);
            }

            $em->persist($cat);
            $em->flush();

            return $this->redirectToRoute('app_create_cat_success');
        }

        return $this->render('page/create_cat.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/created-cat', name: 'app_create_cat_success')]
    #[Template('page/create_cat_success.html.twig')]
    public function createCatSuccess(): array {
        return [];
    }
}
