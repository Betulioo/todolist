<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TareaController extends AbstractController
{
    #[Route("/tareas")]
    public function getTareas()
    {
        $tasks = file_get_contents($this->getParameter("kernel.project_dir")."/data/tasks.json");

        return new Response($tasks, headers: ['Content-Type' => 'application/json']);
    }

    #[Route("/tarea", methods: ['POST'])]
    public function createTarea(Request $request)
    {
        $tasks = file_get_contents($this->getParameter("kernel.project_dir")."/data/tasks.json");
        $arrayTasks = json_decode($tasks, true);

        $newTask = $request->getContent();
        $arrayTask = json_decode($newTask, true);
        $arrayTask['identificador'] = uniqid();

        $arrayTasks['tareas'][] = $arrayTask;

        file_put_contents($this->getParameter("kernel.project_dir")."/data/tasks.json", json_encode($arrayTasks));
        return new Response("OK");
    }

    #[Route("/tarea/{id}", methods: ['DELETE'])]
    public function removeTarea($id)
    {
        $tasks = file_get_contents($this->getParameter("kernel.project_dir")."/data/tasks.json");
        $arrayTasks = json_decode($tasks, true);

        $removeId = false;
        foreach ($arrayTasks['tareas'] as $key => $task){
            if ($task['identificador'] == $id){
                $removeId = $key;
            }
        }

        if ($removeId !== false){
            unset($arrayTasks['tareas'][$removeId]);
            file_put_contents($this->getParameter("kernel.project_dir")."/data/tasks.json", json_encode($arrayTasks));
        }

        return new Response("OK");
    }

    #[Route("/tarea/{id}/done")]
    public function hacerTarea($id)
    {
        $tasks = file_get_contents($this->getParameter("kernel.project_dir")."/data/tasks.json");
        $arrayTasks = json_decode($tasks, true);

        $removeId = false;
        foreach ($arrayTasks['tareas'] as $key => $task){
            if ($task['identificador'] == $id){
                $arrayTasks['tareas'][$key]['estado'] = 'hecha';
                file_put_contents($this->getParameter("kernel.project_dir")."/data/tasks.json", json_encode($arrayTasks));
            }
        }

        return new Response("OK");
    }
}
