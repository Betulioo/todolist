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

        return new Response($tasks, headers: ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);
    }

    #[Route("/tarea/{id}", methods: ['GET'])]
    public function getById($id)
    {

        $tasks = file_get_contents($this->getParameter("kernel.project_dir") . "/data/tasks.json");
        $arrayTasks = json_decode($tasks, true);

        $foundTask = null;
        foreach ($arrayTasks['tareas'] as $task) {
            if ($task['identificador'] == $id) {
                $foundTask = $task;
                break;
            }
        }

        if ($foundTask === null) {
            return new Response("Tarea no encontrada", Response::HTTP_NOT_FOUND);
        }

        return new Response(json_encode($foundTask), headers: ['Content-Type' => 'application/json']);

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

        
        return new Response("OK", headers: ['Access-Control-Allow-Methods'=> 'POST','Access-Control-Allow-Origin' => '*'
     ]);
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

        foreach ($arrayTasks['tareas'] as $key => $task){
            if ($task['identificador'] == $id){
                $arrayTasks['tareas'][$key]['estado'] = 'hecha';
                file_put_contents($this->getParameter("kernel.project_dir")."/data/tasks.json", json_encode($arrayTasks));
            }
        }

        return new Response("OK");
    }

    #[Route("/tarea/{id}/undone")]
    public function tareaSinHacer($id)
    {
        $tasks = file_get_contents($this->getParameter("kernel.project_dir")."/data/tasks.json");
        $arrayTasks = json_decode($tasks, true);

        foreach ($arrayTasks['tareas'] as $key => $task){
            if ($task['identificador'] == $id){
                $arrayTasks['tareas'][$key]['estado'] = 'Sin Hacer';
                file_put_contents($this->getParameter("kernel.project_dir")."/data/tasks.json", json_encode($arrayTasks));
            }
        }

        return new Response("OK");
    }

    #[Route("/tarea/{id}", methods:['PUT'])]
    public function editTarea(Request $request, $id)
    {
        $tasks = file_get_contents($this->getParameter("kernel.project_dir")."/data/tasks.json");
        $arrayTasks = json_decode($tasks, true);

        $updatedTask = json_decode($request->getContent(), true);

        foreach ($arrayTasks['tareas'] as $key => $task) {
           if($task['identificador'] == $id){
            $arrayTasks['tareas'][$key] = array_merge($task, $updatedTask);
                file_put_contents($this->getParameter("kernel.project_dir") . "/data/tasks.json", json_encode($arrayTasks));
                return new Response("Tarea actualizada", Response::HTTP_OK);
           }
        }
        return new Response("Tarea no encontrada", Response::HTTP_NOT_FOUND);
    }
}
