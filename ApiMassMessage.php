<?php
/**
 * API module to send MassMessages
 *
 * @file
 * @ingroup API
 * @author Kunal Mehta
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
class ApiMassMessage extends ApiBase {
	public function execute() {
		if ( !$this->getUser()->isAllowed( 'massmessage') ) {
			$this->dieUsageMsg( 'permissiondenied' );
		}

		$data = $this->extractRequestParams();

		$status = new Status();
		MassMessage::verifyData( $data, $status );
		if ( !$status->isOK() ) {
			$this->dieStatus( $status );
		}

		$count = MassMessage::submit( $this->getContext(), $data );

		$this->getResult()->addValue(
			null,
			$this->getModuleName(),
			array( 'result' => 'success', 'count' => $count )
		);
	}

	public function getDescription() {
		return 'Send a message to a list of pages';
	}

	public function getAllowedParams() {
		return array(
			'spamlist' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'subject' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'message' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			),
			'token' => null,
		);
	}

	public function getParamDescription() {
		return array(
			'spamlist' => 'Page containing list of pages to leave a message on',
			'subject' => 'Subject line of the message',
			'message' => 'Message body text',
			'token' => 'An edit token from action=tokens'
		);
	}

	public function getPossibleErrors() {
		return array(
			array( 'permissiondenied'),
			array( 'massmessage-spamlist-doesnotexist' )
		);
	}

	public function getResultProperties() {
		return array(
			'' => array(
				'result' => 'string',
				'count' => 'integer'
			)
		);
	}

	public function mustBePosted() {
		return true;
	}


	public function needsToken() {
		return true;
	}

	public function getTokenSalt() {
		return '';
	}

	public function isWriteMode() {
		return true;
	}

	public function getExamples() {
		return array(
			'api.php?action=massmessage&spamlist=Signpost%20Spamlist&subject=New%20Signpost&message=Please%20read%20it&token=TOKEN'
			=> 'Send a message to the list at [[Signpost Spamlist]] with the subject "New Signpost", and message body of "Please read it"'
		);
	}

	public function getHelpUrls() {
		return array( 'https://www.mediawiki.org/wiki/Extension:MassMessage/API' );
	}

}
