<?php
/**
 * Google Analytics API Admin List
 *
 * @author  Kazuya Takami
 * @since   1.0.0
 * @see     wp-posted-display-admin-db.php
 */
class Wp_Google_Analytics_Api_Admin_List {

	/**
	 * Variable definition.
	 *
	 * @since 1.0.0
	 */
	private $text_domain;

	/**
	 * Constructor Define.
	 *
	 * @since   1.0.0
	 * @param   String $text_domain
	 */
	public function __construct ( $text_domain ) {
		// Load the Google API PHP Client Library.
		require_once( plugin_dir_path( __FILE__ ) . '../vendor/autoload.php' );

		$this->text_domain = $text_domain;

		$analytics = $this->initialize_analytics();
		$response  = $this->get_report( $analytics );
		$this->page_render( $response );
	}

	/**
	 * Creates and returns the Analytics Reporting service object.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  Google_Service_AnalyticsReporting $analytics
	 */
	public function initialize_analytics () {
		// Use the developers console and download your service account
		// credentials in JSON format. Place them in this directory or
		// change the key file location if necessary.
		$KEY_FILE_LOCATION = plugin_dir_path( __FILE__ ) . '../My Project-5832058faf4b.json';

		// Create and configure a new client object.
		$client = new Google_Client();
		$client->setApplicationName( "Hello Analytics Reporting" );
		$client->setAuthConfig( $KEY_FILE_LOCATION );
		$client->setScopes( ['https://www.googleapis.com/auth/analytics.readonly'] );
		$analytics = new Google_Service_AnalyticsReporting( $client );

		return $analytics;
	}

	/**
	 * Get Report.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   Google_Service_AnalyticsReporting $analytics
	 * @return  Google_Service_AnalyticsReporting $analytics
	 */
	public function get_report( $analytics ) {
		// Replace with your view ID, for example XXXX.
		$VIEW_ID = "95507515";

		// Create the DateRange object.
		$dateRange = new Google_Service_AnalyticsReporting_DateRange();
		$dateRange->setStartDate( "7daysAgo" );
		$dateRange->setEndDate( "today" );

		// Create the Metrics object.
		$sessions = new Google_Service_AnalyticsReporting_Metric();
		$sessions->setExpression( "ga:sessions" );
		$sessions->setAlias( "sessions" );

		// Create the ReportRequest object.
		$request = new Google_Service_AnalyticsReporting_ReportRequest();
		$request->setViewId( $VIEW_ID );
		$request->setDateRanges( $dateRange );
		$request->setMetrics( array( $sessions ) );

		$body = new Google_Service_AnalyticsReporting_GetReportsRequest();
		$body->setReportRequests( array( $request ) );
		return $analytics->reports->batchGet( $body );
	}

	/**
	 * LIST Page HTML Render.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @param   $reports
	 */
	private function page_render( $reports ) {
		$html  = '';
		$html .= '<div class="wrap">';
		$html .= '<h1>' . esc_html__( 'Posted Display Settings List', $this->text_domain );
		$html .= '</h1>';
		echo $html;

		for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
			$report = $reports[ $reportIndex ];
			$header = $report->getColumnHeader();
			$dimensionHeaders = $header->getDimensions();
			$metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
			$rows = $report->getData()->getRows();

			for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
				$row = $rows[ $rowIndex ];
				$dimensions = $row->getDimensions();
				$metrics = $row->getMetrics();
				for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
					print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
				}

				for ($j = 0; $j < count( $metricHeaders ) && $j < count( $metrics ); $j++) {
					$entry = $metricHeaders[$j];
					$values = $metrics[$j];
					print("Metric type: " . $entry->getType() . "\n" );
					for ( $valueIndex = 0; $valueIndex < count( $values->getValues() ); $valueIndex++ ) {
						$value = $values->getValues()[ $valueIndex ];
						print($entry->getName() . ": " . $value . "\n");
					}
				}
			}
		}

		$html .= '</div>';
		echo $html;
	}
}