<?php

declare(strict_types=1);

namespace Kkguan\PHPMapstruct\Processor\Internal\Processor;

use Kkguan\PHPMapstruct\Processor\Internal\Model\Mapper;
use PhpParser\Error;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Filesystem\Filesystem;

class MapperRenderingProcessor implements ModelElementProcessor
{
    private ProcessorContext $context;

    private Parser $parser;

    public function __construct()
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    }

    public function process(ProcessorContext $context, \ReflectionClass $mapperTypeElement, $sourceModel)
    {
        $this->context = $context;
        if (! $context->isErroneous()) {
            $this->writeToSourceFile($context->getFiler(), $sourceModel, $mapperTypeElement);
            return $sourceModel;
        }
        return null;
    }

    public function getPriority(): int
    {
        return 9999;
    }

    private function writeToSourceFile(Filesystem $filer, Mapper $sourceModel, \ReflectionClass $originatingElement)
    {
        $this->createSourceFile($sourceModel, $filer, $originatingElement);
    }

    private function createSourceFile(Mapper $sourceModel, Filesystem $filer, \ReflectionClass $originatingElement)
    {
        // 组装文件名称
//        $namespace = '';
//        if ($sourceModel->getBuilder()->getTypeElement()->getNamespaceName() !== null) {
//            $namespace .= $sourceModel->getBuilder()->getTypeElement()->getNamespaceName() . '\\';
//        }

        $namespace = $sourceModel->getBuilder()->getTypeElement()->getNamespaceName();

        $this->initCacheDir();
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../Model/');
        $twig = new \Twig\Environment($loader, [
            'cache' => $this->context->getOptions()->getGeneratedSourcesDirectory() . '/cache',
            'debug' => true,
            'autoescape' => false,
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        $className = (string) last(explode('\\', $originatingElement->getName()));

//        /** @var BeanMappingMethod $method */
//        $method = $sourceModel->getBuilder()->getMethods()[0];

//        foreach ($sourceModel->getBuilder()->getTypeElement()->getProperties() as $property) {
//            dump($property->getName());
//            dump($property->getType()->getName());
//            dump($property->getType());
//        }

        $code = $twig->render('GeneratedType.twig', [
            'namespace' => $namespace,
            'builder' => $sourceModel->getBuilder(),
            //            'methods' => $sourceModel->getBuilder()->getMethods(),
            'className' => $className,
        ]);

        try {
            $ast = $this->parser->parse($code);
        } catch (Error $error) {
            $this->context->getOptions()->getLogger()->error(sprintf('Parse error:%s', $error->getMessage()));
            return;
        }

        $prettyPrinter = new Standard();
        $code = $prettyPrinter->prettyPrintFile($ast);
        $gemPath = sprintf('%s/%sImpl.php', $this->initGemDir($namespace), $className);
        file_put_contents($gemPath, $code);
    }

    private function initCacheDir()
    {
        $path = $this->context->getOptions()->getGeneratedSourcesDirectory();
        if (! is_dir($path . '/cache')) {
            mkdir($path . '/cache', 0777, true);
        }
    }

    private function initGemDir(string $namespace): string
    {
        $gemPath = $this->context->getOptions()->getGeneratedSourcesDirectory();
        $path = str_replace('\\', '/', $namespace);
        $dirPath = $gemPath . '/gem/' . $path;
        if (! is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        return $dirPath;
    }
}
