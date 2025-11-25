<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Limbas\Controllers;

use eftec\bladeone\BladeOne;
use Exception;
use Psr\Container\ContainerInterface;
use SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

abstract class AbstractController
{
    protected ContainerInterface $container;


    /**
     * Create dynamic Response
     * 
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return Response
     */
    protected function respond(mixed $data, int $status = 200, array $headers = []): Response
    {
        if(is_array($data) || is_object($data)) {
            return $this->json($data, $status, $headers);
        }
        return new Response($data, $status, $headers);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url
     * @param int $status The HTTP status code (302 "Found" by default)
     * @return RedirectResponse
     */
    protected function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param string $route
     * @param array $parameters
     * @param int $status The HTTP status code (302 "Found" by default)
     * @return RedirectResponse
     * @throws Exception
     */
    protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        return $this->redirect(route($route, $parameters), $status);
    }


    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     *
     * @param mixed $data
     * @param int $status The HTTP status code (200 "OK" by default)
     * @param array $headers
     * @return JsonResponse
     */
    protected function json(mixed $data, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Returns a BinaryFileResponse object with original or customized file name and disposition header.
     * 
     * @param SplFileInfo|string $file
     * @param string|null $fileName
     * @param string $disposition
     * @return BinaryFileResponse
     */
    protected function file(SplFileInfo|string $file, ?string $fileName = null, string $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT): BinaryFileResponse
    {
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition($disposition, $fileName ?? $response->getFile()->getFilename());

        return $response;
    }

    /**
     * Renders and output view.
     *
     * @param string $view
     * @param array $parameters
     * @param int $status
     * @param array $headers
     * @return Response
     * @throws Exception
     */
    protected function render(string $view, array $parameters = [], int $status = 200, array $headers = []): Response
    {
        $content = $this->doRenderView($view, $parameters);
        return $this->respond($content, $status, $headers);
    }


    /**
     * Renders a view.
     * 
     * @param string $view
     * @param array $parameters
     * @return string
     * @throws Exception
     */
    private function doRenderView(string $view, array $parameters): string
    {
        $pathParts = pathinfo($view);
        $templatePath = str_replace('.','',$pathParts['dirname'] ?? '') ?: COREPATH . 'resources/views';
        $view = $pathParts['basename'];
        
        if(!is_dir(TEMPPATH . 'views/cache')) {
            mkdir(TEMPPATH . 'views/cache', 0755, true);
        }
        
        $blade = new BladeOne($templatePath,TEMPPATH . 'views/cache',BladeOne::MODE_AUTO);

        return $blade->run($view,$parameters);
    }
    
}
