<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Curl\MultiCurl;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\History;
use DB;

class HomeController extends Controller
{
	/**
	 * @var $multiCurl
	 */
	public $multiCurl;

	/**
	 * @var array
	 */
	public $currencyNames = array();

	/**
	 * @var int
	 */
	public $defaultCountDays = 3;

	/**
	 * @var int
	 */
	public $allDays = 14;

	/**
	 * @var array
	 */
	public $errors = array();

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$currencies = $this->checkHistory();

		$first = $currencies->first();

		$defaultData = $this->getData( $first, $this->defaultCountDays );

		$firstName = $first != null ? $first->name : '';

		return view( 'home', [
			'currencies' => $currencies,
			'firstName'  => $firstName,
			'rates'      => $defaultData['rates'],
			'labels'     => $defaultData['labels']
		] );
	}

	/**
	 * @param Request $request
	 *
	 * @return array
	 */
	public function ajaxDataInPeriod( Request $request ) {
		if ( $request->ajax() ) {
			$id            = $request->get( 'id' );
			$requestPeriod = $request->get( 'period' );
			$period        = $requestPeriod == 'all' ? $this->allDays : $requestPeriod;
			$currency      = Currency::find( $id );
			$data          = $this->getData( $currency, $period );

			return array(
				'rates'  => $data['rates'],
				'labels' => $data['labels']
			);
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return array
	 */
	public function ajaxDefaultData( Request $request ) {
		if ( $request->ajax() ) {
			$id       = $request->get( 'id' );
			$currency = Currency::find( $id );
			$data     = $this->getData( $currency, $this->defaultCountDays );

			return array(
				'rates'  => $data['rates'],
				'labels' => $data['labels']
			);
		}
	}

	/**
	 * @param object $currency
	 * @param integer $countDays
	 *
	 * @return array
	 */
	public function getData( $currency, $countDays )
	{
		$rates  = [];
		$labels = [];

		if ( $currency != null ) {
			$histories = $currency->histories->sortBy( 'date' );

			foreach ( $histories as $history ) {
				if ( $history->date > Carbon::now()->subDays( $countDays ) ) {
					$rates[]  = $history->rate;
					$time     = strtotime( $history->date );
					$labels[] = date( 'd.m', $time );
				}
			}
		}

		return array(
			'rates'  => join( ',', $rates ),
			'labels' => join( ',', $labels )
		);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public function checkHistory()
	{
		$currencies = Currency::all();

		$this->multiCurl = new MultiCurl;

		$this->historySystem( $currencies );

		$this->multiCurl->start();

		return Currency::all();
	}

	/**
	 * @param Currency[] $currencies
	 */
	public function historySystem( $currencies )
	{
		$checkFullHistoryWork = array();

		foreach ( $currencies as $currency ) {
			$this->currencyNames[] = array(
				'name' => strtoupper( $currency->name ),
				'id'   => $currency->id
			);

			if ( ! count( $currency->histories ) ) {
				$this->createCurrencyHistory( $currency, $this->allDays );
			} else {
				$checkFullHistoryWork[] = 1;
			}
		}

		if ( count( $checkFullHistoryWork ) ) {
			$this->newRates( $currencies );
		}
	}

	/**
	 * @param object $currency
	 * @param integer $max
	 */
	public function createCurrencyHistory( $currency, $max )
	{
		for ( $i = 0; $i <= $max; $i ++ ) {

			$date = $i < 1 ? date( 'Ymd', strtotime( 'now' ) ) : date( 'Ymd', strtotime( "- {$i} day" ) );

			$this->multiCurlWork( $date, $currency );
		}
	}

	/**
	 * @param Currency[] $currencies
	 */
	public function newRates( $currencies )
	{
		$results = DB::select( "SELECT id FROM `histories` WHERE DATE(`date`) = CURDATE() LIMIT 1" );

		if ( ! count( $results ) ) {

			$date = date( 'Ymd', strtotime( 'now' ) );

			foreach ( $currencies as $currency ) {

				$this->multiCurlWork( $date, $currency );
			}

			DB::delete( "DELETE FROM `histories` WHERE DATE(`date`) < NOW() - INTERVAL 15 DAY" );
		}
	}

	/**
	 * @param string $date
	 * @param object $currency
	 */
	public function multiCurlWork( $date, $currency )
	{
		$this->multiCurl->addGet( "https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?valcode={$currency->name}&date={$date}&json" );

		$this->curlSuccess();

		$this->curlError();
	}

	/**
	 * @return void
	 */
	public function curlSuccess()
	{
		$this->multiCurl->success( function ( $data ) {
			$res = $data->response;

			if ( count( $res ) ) {

				$rate         = $res[0]->rate;
				$date         = date( "Y-m-d", strtotime( $res[0]->exchangedate ) );
				$currencyName = $res[0]->cc;

				$needId = array();
				foreach ( $this->currencyNames as $key => $value ) {
					if ( $value['name'] == $currencyName ) {
						$needId[] = $value['id'];
						break;
					}
				}

				if ( count( $needId ) ) {
					History::create( [
						'Currency' => $needId[0],
						'date'     => $date,
						'rate'     => $rate
					] );
				}
			}
		} );
	}

	/**
	 * @return void
	 */
	public function curlError()
	{
		$this->multiCurl->error( function ( $data ) {
			$this->errors[] = array(
				'url'          => $data->url,
				'errorCode'    => $data->errorCode,
				'errorMessage' => $data->errorMessage
			);
		} );
	}
}
