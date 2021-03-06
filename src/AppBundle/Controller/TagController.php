<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Tag;
use AppBundle\Form\Type\TagType;
use AppBundle\Repository\TagRepositoryInterface;
use QafooLabs\MVC\Flash;
use QafooLabs\MVC\FormRequest;
use QafooLabs\MVC\RedirectRoute;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TagController
{
    private $tagRepository;

    /**
     * @param TagRepositoryInterface $tagRepository
     */
    public function __construct(
        TagRepositoryInterface $tagRepository
    ) {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @return array
     */
    public function homeScreenAction()
    {
        return [
            'tags' => $this->tagRepository->findAll(),
        ];
    }

    /**
     * @param FormRequest $formRequest
     * @param Flash       $flash
     *
     * @return RedirectResponse|array
     */
    public function createTagAction(FormRequest $formRequest, Flash $flash)
    {
        if (!$formRequest->handle(TagType::class, new Tag())) {
            return ['form' => $formRequest->createFormView()];
        }

        $tag = $formRequest->getValidData();

        $this->tagRepository->save($tag);
        $flash->add(
            'notice',
            'Tag wurde erfolgreich erstellt.'
        );

        return new RedirectRoute('tag_home_screen');
    }
}
