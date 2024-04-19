<?php

namespace Drupal\shared\EventSubscriber;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class EventSubscriber.
 */
class EventSubscriber implements EventSubscriberInterface
{

  protected $currentUser;

  /**
   * Constructs a new EventSubscriber object.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   */
  public function __construct(AccountInterface $current_user)
  {

    $this->currentUser = $current_user;

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {

    $events[KernelEvents::RESPONSE][] = ['redirectIf'];
    return $events;

  }

  public function redirectIf($event)
  {

    $node = $event->getRequest()->attributes->get('node');
    if ($node) {
      if (is_object($node)) {
        if ($node->get('nid')->value == 1) {
          if ($this->currentUser->isAnonymous()) {
          }
          elseif ($this->currentUser->id()=="1") {
          }
          else {
            $path = Url::fromUserInput('/node/25')->toString();
            $event->setResponse(new RedirectResponse($path));
          }
        }
        if ($node->get('nid')->value == 196) {
          if ($this->currentUser->isAnonymous()) {
          }
          elseif ($this->currentUser->id()=="1") {
          }
          else {
            $path = Url::fromUserInput('/association/membership0')->toString();
            $event->setResponse(new RedirectResponse($path));
          }
        }
      }
    }

  }

  /**
   * This method is called when the event_subscriber is dispatched.
   *
   * @param \Symfony\Component\EventDispatcher\Event $event
   *   The dispatched event.
   */
  public function eventSubscriber(Event $event)
  {
    \Drupal::messenger()
      ->addMessage('Event event_subscriber thrown by Subscriber in module shared.', 'status', TRUE);
  }

}
