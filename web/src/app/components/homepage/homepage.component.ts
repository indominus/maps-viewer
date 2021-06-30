import {Component, OnInit} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {ActivatedRoute} from '@angular/router';
import {HttpClient} from '@angular/common/http';
import {URLS} from '../../constant';

@Component({
  selector: 'app-homepage',
  templateUrl: './homepage.component.html',
  styleUrls: ['./homepage.component.scss']
})
export class HomepageComponent implements OnInit {

  response: any;
  form: FormGroup;
  providers: { id: number, name: string }[] = [];
  currentProvider: { id: number, name: string } | undefined;

  constructor(private httpClient: HttpClient,
              private formBuilder: FormBuilder,
              private activatedRoute: ActivatedRoute
  ) {

    this.httpClient.get(URLS.PROVIDERS).subscribe((data: any) => {
      this.providers = data;
      const providerId = this.activatedRoute.snapshot.queryParams.provider || this.providers[0].id;
      this.setCurrentProvider(providerId);
    });

    this.form = this.formBuilder.group({
      query: ['']
    });
  }

  ngOnInit(): void {
    this.activatedRoute.queryParams.subscribe((params: any) => {
      this.setCurrentProvider(params?.provider);
      this.onSearch();
    });
  }

  setCurrentProvider(providerId: string): void {
    const data = this.providers.find((provider: any) => provider.id.toString() === providerId);
    this.currentProvider = data || this.providers[0];
  }

  onSearch(): void {
    const data = Object.assign({}, this.form.getRawValue(), {provider: this.currentProvider?.id});
    this.httpClient.post(URLS.MATCH_ADDRESS, data).subscribe((response: any) => {
      this.response = response;
    });
  }
}
