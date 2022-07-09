<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use App\Managers\GroupManager;
use App\Repository\FigureRepository;
use App\Repository\GroupRepository;
use http\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GroupController
 * @package App\Controller
 * @Route ("/group")
 */
class GroupController extends AbstractController
{
    /** @var GroupRepository  */
    private $groupRepository;

    /**
     * GroupController constructor.
     * @param GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @Route("/", name="group_list")
     */
    public function list(): Response
    {
        return $this->render('group/index.html.twig', [
            'controller_name' => 'GroupController',
            'groups' => $this->groupRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="group_new", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param GroupManager $groupManager
     * @return Response
     */
    public function new(Request $request, GroupManager $groupManager): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupManager->save($group);
            return $this->redirectToRoute('group_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/new.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="figures_group", methods={"GET"})
     * @param Group $group
     * @param FigureRepository $figureRepository
     * @return Response
     */
    public function showByGroup(Group $group, FigureRepository $figureRepository): Response
    {
        return $this->render('figure/list.html.twig', [
            'figures' => $figureRepository->findBy(['group' => $group->getId()])
        ]);
    }

    /**
     * @Route("/{id}/edit", name="group_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param GroupManager $groupManager
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, GroupManager $groupManager): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupManager->save($group);
            return $this->redirectToRoute('group_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/edit.html.twig', [
            'grop' => $group,
            'form' => $form,
        ]);
    }
}
