# MailBundle
Provides functional for send emails using email templates and less generating functionality for end emails

## Installation and configuration:

Pretty simple with [Composer](http://packagist.org), run:

```sh
composer require ite/mail-bundle
```

<a name="configuration"></a>

### Configuration example

You can configure default parameters for email senders and using styles for email templates

```yaml
ite_mail:
    bcc_email: %bcc_email%
    from_email: %support_email%
    support_email: %support_email%
    noreply_email: %noreply_email%
    template_folder: AcmeCoreBundle:Email/Template #folder with all email templates
    styles: ['@AcmeCoreBundle/Resources/public/less/email/style.less', '@AcmeCoreBundle/Resources/public/less/email/style2.less'] #additional styles for email templates will be generated to inline styles in end email
    translation_domain: email_subjects #provide translations for end email subjects
```

### Add ITEMailBundle to your application kernel

```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
            new ITE\MailBundle\ITEMailBundle(),
        // ...
    );
}
```

## Usage examples:

### Add Extension

Create tokens based on context name

```php
// src/Acme/CoreBundle/Extension/Mail
namespace Acme\CoreBundle\Extension\Mail;

use ITE\MailBundle\Extension\BaseExtension;
use ITE\MailBundle\Extension\TokenExtensionInterface;
use ITE\MailBundle\Token\Context;
use ITE\MailBundle\Token\Token;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MainExtension extends BaseExtension implements TokenExtensionInterface
{

    private $twig;
    
    private $container;
    
    /**
     * @param ContainerInterface $container
     * @internal param $twig
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->twig = $container->get('twig');
    }

    /**
     * Retrieve token list depend on token context
     *
     * @param Context $context
     *
     * @return Token[]
     */
    public function getTokens(Context $context)
    {
        $this->setContext($context);
        $data = $context->getData();
        switch ($context->getName()) {
            case 'test_context_name':
                return [
                    new Token('test_token', $data['someData'], 'Test Token'),
                ];
                break;
        }

        return [];
    }

    public function setContext(Context $context)
    {
        if ($context->getData()) {
            return null;
        }
    }

    /**
     * Retrieve global token list, not depended on context
     *
     * @return Token[]
     */
    public function getGlobals()
    {
        return [];
    }

} 
```

### Add Extension to Services

Add mail extension to services.yml
 
```yaml
services:
    admin.mail.token.main_extension:
        class: Acme\CoreBundle\Extension\Mail\MainExtension
        arguments: [@service_container]
        tags:
            - {name: ite.mail.extension}
```

### Add template to AcmeCoreBundle:Email/Template folder (template_folder in config.yml)

Create the template test_template.html.twig in AcmeCoreBundle:Email/Template folder

```jinja
Hi User,
[test_token] {#This is the token name from Acme\CoreBundle\Extension\Mail\MainExtension#}
```

### Controller

You can send email using template test_template.html.twig:

```php
// Acme\MainBundle\Controller\ArticleController.php

    public function sendAction()
    {
        $data = ['someData' => 123];
        $this
            ->get('ite.mail.manager')
            ->mail('test_template', 'some_email@mail.com', 'Some subject', [
                'token_context' => new Context($data, 'test_context_name')
            ]);

        return [];
    }

```

