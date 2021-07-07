<style>
    #map {
        height: 400px;
        width: 100%;
    }
</style>

@php
    $showAutocomplete = property_exists($row->details, 'showAutocompleteInput') ? (bool)$row->details->showAutocompleteInput : true;
    $showAutocomplete = $showAutocomplete ? 'true' : 'false';
    $showLatLng = property_exists($row->details, 'showLatLngInput') ? (bool)$row->details->showLatLngInput : true;
    $showLatLng = $showLatLng ? 'true' : 'false';
@endphp

<div id="coordinates-formfield-{{ $row->field }}">
    <coordinates
        inline-template
        ref="coordinates"
        api-key="{{ config('voyager.googlemaps.key') }}"
        :points='@json($dataTypeContent->getCoordinates() && count($dataTypeContent->getCoordinates()) ? $dataTypeContent->getCoordinates() : [[ 'lat' => config('voyager.googlemaps.center.lat'), 'lng' => config('voyager.googlemaps.center.lng') ]])'
        :show-autocomplete="{{ $showAutocomplete }}"
        :show-lat-lng="{{ $showLatLng }}"
        :zoom={{ config('voyager.googlemaps.zoom') }}
    >
        <div>
            <div class="form-group">
                <div class="col-md-5" v-if="showAutocomplete">
                    <label class="control-label">{{ __('voyager::generic.find_by_place') }}</label>
                    <input
                        class="form-control"
                        type="text"
                        placeholder="742 Evergreen Terrace"
                        id="places-autocomplete"
                        v-on:keypress="onInputKeyPress($event)"
                    />
                </div>
                <div class="col-md-2" v-if="showLatLng">
                    <label class="control-label">{{ __('voyager::generic.latitude') }}</label>
                    <input
                        class="form-control"
                        type="number"
                        step="any"
                        name="{{ $row->field }}[lat]"
                        placeholder="19.6400"
                        v-model="lat"
                        @change="onLatLngInputChange"
                        v-on:keypress="onInputKeyPress($event)"
                    />
                </div>
                <div class="col-md-2" v-if="showLatLng">
                    <label class="control-label">{{ __('voyager::generic.longitude') }}</label>
                    <input
                        class="form-control"
                        type="number"
                        step="any"
                        name="{{ $row->field }}[lng]"
                        placeholder="-155.9969"
                        v-model="lng"
                        @change="onLatLngInputChange"
                        v-on:keypress="onInputKeyPress($event)"
                    />
                </div>

                <div class="clearfix"></div>
            </div>

            <div id="map"></div>
        </div>
    </coordinates>
</div>

@push('javascript')
    <script>
        Vue.component('coordinates', {
            props: {
                apiKey: {
                    type: String,
                    required: true,
                },
                points: {
                    type: Array,
                    required: true,
                },
                showAutocomplete: {
                    type: Boolean,
                    default: true,
                },
                showLatLng: {
                    type: Boolean,
                    default: true,
                },
                zoom: {
                    type: Number,
                    required: true,
                }
            },
            data() {
                return {
                    autocomplete: null,
                    lat: '',
                    lng: '',
                    map: null,
                    marker: null,
                    onChangeDebounceTimeout: null,
                    place: null,
                };
            },
            mounted() {
                // Load Google Maps script
                const apiUrl = 'https://maps.googleapis.com/maps/api/js?key='+this.apiKey+'&callback=gMapVm.$refs.coordinates.initMap&libraries=places';
                if (document.querySelectorAll('[src="' + apiUrl + '"]').length > 0) {
                    return;
                }

                let gMapScript = document.createElement('script');
                gMapScript.setAttribute('src', apiUrl);
                document.head.appendChild(gMapScript);
            },
            methods: {
                initMap: function() {
                    var vm = this;

                    var center = vm.points[vm.points.length - 1];

                    // Set initial LatLng
                    this.setLatLng(center.lat, center.lng);

                    // Create map
                    vm.map = new google.maps.Map(document.getElementById('map'), {
                        zoom: vm.zoom,
                        center: new google.maps.LatLng(center.lat, center.lng)
                    });

                    // Create marker
                    vm.marker = new google.maps.Marker({
                        position: new google.maps.LatLng(center.lat, center.lng),
                        map: vm.map,
                        draggable: true
                    });

                    // Listen to map drag events
                    google.maps.event.addListener(vm.marker, 'drag', vm.onMapDrag);

                    // Setup places Autocomplete
                    if (this.showAutocomplete) {
                        vm.autocomplete = new google.maps.places.Autocomplete(document.getElementById('places-autocomplete'));
                        places = new google.maps.places.PlacesService(vm.map);
                        vm.autocomplete.addListener('place_changed', vm.onPlaceChange);
                    }
                },

                setLatLng: function(lat, lng) {
                    this.lat = lat;
                    this.lng = lng;
                },

                moveMapAndMarker: function(lat, lng) {
                    this.marker.setPosition(new google.maps.LatLng(lat, lng));
                    this.map.panTo(new google.maps.LatLng(lat, lng));
                },

                onMapDrag: function(event) {
                    this.setLatLng(event.latLng.lat(), event.latLng.lng());

                    this.onChange('mapDragged');
                },

                onInputKeyPress: function(event) {
                    if (event.which === 13) {
                        event.preventDefault();
                    }
                },

                onPlaceChange: function() {
                    this.place = this.autocomplete.getPlace();

                    if (this.place.geometry) {
                        this.setLatLng(this.place.geometry.location.lat(), this.place.geometry.location.lng());
                        this.moveMapAndMarker(this.place.geometry.location.lat(), this.place.geometry.location.lng());
                    }

                    this.onChange('placeChanged');
                },

                onLatLngInputChange: function(event) {
                    this.moveMapAndMarker(this.lat, this.lng);

                    this.onChange('latLngChanged');
                },

                onChange: function(eventType) {
                    @if (property_exists($row->details, 'onChange'))
                        if (this.onChangeDebounceTimeout) {
                            clearTimeout(this.onChangeDebounceTimeout);
                        }

                        self = this
                        this.onChangeDebounceTimeout = setTimeout(function() {
                            {{ $row->details->onChange }}(eventType, {
                                lat: self.lat,
                                lng: self.lng,
                                place: self.place
                            });
                        }, 300);
                    @endif
                },
            }
        });

        var gMapVm = new Vue({ el: '#coordinates-formfield-{{ $row->field }}' });
    </script>
@endpush
