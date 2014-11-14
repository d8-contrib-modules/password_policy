<?php
/**
 * @file
 * Contains Drupal\password_policy\EventSubscriber\PasswordPolicyEventSubcriber.
 */
namespace Drupal\password_policy\EventSubscriber;

use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Routing\CurrentRouteMatch;

class PasswordPolicyEventSubscriber implements EventSubscriberInterface {

	/**
	 * Event callback to look for users expired password
	 */
	public function checkForUserPasswordExpiration(GetResponseEvent $event) {
		$account = \Drupal::currentUser();
		$uid = $account->id();
		$request = \Drupal::request();
		//dpm($uid);
		if ($uid and CurrentRouteMatch::) {
			//TODO - Implement caching for expiration, this should be a cache.get around uid in lieu of db hit
			$expired_user = db_select("password_policy_user_reset", 'p')
				->fields('p', array())
				->condition('uid', $uid)
				->condition('expired', '1')
				->execute();

			if($expired_user->fetch()){
				$url = new Url('entity.user.edit_form', array('user'=>$uid));
				$event->setResponse(new RedirectResponse($url));
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	static function getSubscribedEvents() {
		//TODO - Evaluate if there is a better place to add this check
		$events[KernelEvents::REQUEST][] = array('checkForUserPasswordExpiration');
		return $events;
	}
}